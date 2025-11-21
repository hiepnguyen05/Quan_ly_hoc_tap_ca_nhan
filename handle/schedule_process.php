<?php
session_start();
require_once '../functions/auth.php';
require_once '../functions/schedule_functions.php';

// Helpers
function redirectWithMessage($path, $type, $message) {
    if ($message) {
        $_SESSION[$type . '_message'] = $message;
    }
    header('Location: ' . $path);
    exit();
}

function requirePost() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirectWithMessage('../views/schedule/schedule.php', 'error', 'Phương thức yêu cầu không hợp lệ.');
    }
}

function collectScheduleFormData() {
    return [
        'id' => (int)($_POST['schedule_id'] ?? 0),
        'name' => trim($_POST['schedule_name'] ?? ''),
        'start' => $_POST['start_date'] ?? null,
        'end' => $_POST['end_date'] ?? null,
        'is_active' => (int)($_POST['is_active'] ?? 1),
        'items' => $_POST['schedule_items'] ?? [],
    ];
}

function persistScheduleItems($scheduleId, array $scheduleItems) {
    $errors = [];

    foreach ($scheduleItems as $day => $timeSlots) {
        foreach ($timeSlots as $timeSlot => $studyPlanId) {
            if (!$studyPlanId) {
                continue;
            }
            if (!addScheduleItem($scheduleId, $studyPlanId, $day, $timeSlot)) {
                $errors[] = "Lỗi khi thêm môn học vào $day - $timeSlot";
            }
        }
    }

    return $errors;
}

function redirectToScheduleList($type = null, $message = null) {
    redirectWithMessage('../views/schedule/schedule.php', $type ?? 'success', $message);
}

// Kiểm tra đăng nhập
checkLogin('../index.php');
$currentUser = getCurrentUser();
$action = $_GET['action'] ?? $_POST['action'] ?? null;

if (!$action) {
    redirectToScheduleList();
}

switch ($action) {
    case 'add_schedule':
        requirePost();
        $data = collectScheduleFormData();

        if (empty($data['name'])) {
            redirectToScheduleList('error', 'Vui lòng nhập tên thời khóa biểu.');
        }

        $scheduleId = createSchedule($currentUser['id'], $data['name'], $data['start'], $data['end'], $data['is_active']);

        if (!$scheduleId) {
            redirectToScheduleList('error', 'Có lỗi xảy ra khi tạo thời khóa biểu. Vui lòng thử lại.');
        }

        if (!empty($data['items'])) {
            $itemErrors = persistScheduleItems($scheduleId, $data['items']);
            if (!empty($itemErrors)) {
                $_SESSION['warning_message'] = 'Thời khóa biểu đã được tạo nhưng có lỗi khi thêm một số môn học: ' . implode(', ', $itemErrors);
            }
        }

        redirectToScheduleList('success', 'Thời khóa biểu đã được tạo thành công!');

    case 'edit_schedule':
        requirePost();
        $data = collectScheduleFormData();

        if (!$data['id']) {
            redirectToScheduleList('error', 'Không tìm thấy thời khóa biểu để cập nhật.');
        }

        if (empty($data['name'])) {
            redirectWithMessage('../views/schedule/edit_schedule.php?id=' . $data['id'], 'error', 'Vui lòng nhập tên thời khóa biểu.');
        }

        $updated = updateSchedule($data['id'], $currentUser['id'], $data['name'], $data['start'], $data['end'], $data['is_active']);

        if ($updated === false) {
            redirectToScheduleList('error', 'Có lỗi xảy ra khi cập nhật thời khóa biểu. Có thể thời khóa biểu không tồn tại hoặc không thuộc quyền của bạn.');
        }

        if (deleteAllScheduleItems($data['id'], $currentUser['id']) === false) {
            redirectToScheduleList('error', 'Có lỗi xảy ra khi cập nhật thời khóa biểu. Không thể xóa các môn học hiện tại.');
        }

        if (!empty($data['items'])) {
            $itemErrors = persistScheduleItems($data['id'], $data['items']);
            if (!empty($itemErrors)) {
                $_SESSION['warning_message'] = 'Thời khóa biểu đã được cập nhật nhưng có lỗi khi thêm một số môn học: ' . implode(', ', $itemErrors);
            } else {
                $_SESSION['success_message'] = 'Thời khóa biểu đã được cập nhật thành công!';
            }
        } else {
            $_SESSION['success_message'] = 'Thời khóa biểu đã được cập nhật thành công!';
        }

        redirectToScheduleList();

    case 'delete_schedule':
        $scheduleId = (int)($_GET['id'] ?? 0);
        if (!$scheduleId) {
            redirectToScheduleList('error', 'Không tìm thấy thời khóa biểu để xóa.');
        }

        $result = deleteSchedule($scheduleId, $currentUser['id']);
        if ($result) {
            redirectToScheduleList('success', 'Thời khóa biểu đã được xóa thành công!');
        }

        redirectToScheduleList('error', 'Có lỗi xảy ra khi xóa thời khóa biểu. Có thể thời khóa biểu không tồn tại hoặc không thuộc quyền của bạn.');

    case 'add_schedule_item':
        requirePost();
        $scheduleId = (int)($_POST['schedule_id'] ?? 0);
        $studyPlanId = (int)($_POST['study_plan_id'] ?? 0);
        $dayOfWeek = $_POST['day_of_week'] ?? '';
        $timeSlot = $_POST['time_slot'] ?? '';

        if ($scheduleId && $studyPlanId && $dayOfWeek && $timeSlot) {
            $result = addScheduleItem($scheduleId, $studyPlanId, $dayOfWeek, $timeSlot);
            $messageType = $result ? 'success' : 'error';
            $message = $result ? 'Môn học đã được thêm vào thời khóa biểu!' : 'Có lỗi xảy ra khi thêm môn học. Vui lòng thử lại.';
        } else {
            $messageType = 'error';
            $message = 'Vui lòng chọn đầy đủ thông tin môn học.';
        }

        $redirectId = (int)($_POST['schedule_id'] ?? $_GET['schedule_id'] ?? 0);
        $path = $redirectId ? '../views/schedule/view_schedule.php?id=' . $redirectId : '../views/schedule/schedule.php';
        redirectWithMessage($path, $messageType, $message);

    case 'delete_schedule_item':
        $itemId = (int)($_GET['id'] ?? 0);
        if (!$itemId) {
            redirectToScheduleList('error', 'Không tìm thấy môn học để xóa.');
        }

        $result = deleteScheduleItem($itemId, $currentUser['id']);
        $messageType = $result ? 'success' : 'error';
        $message = $result ? 'Môn học đã được xóa khỏi thời khóa biểu!' : 'Có lỗi xảy ra khi xóa môn học. Có thể môn học không tồn tại hoặc không thuộc quyền của bạn.';

        $scheduleId = (int)($_GET['schedule_id'] ?? 0);
        $path = $scheduleId ? '../views/schedule/view_schedule.php?id=' . $scheduleId : '../views/schedule/schedule.php';
        redirectWithMessage($path, $messageType, $message);

    default:
        redirectToScheduleList('error', 'Hành động không hợp lệ.');
}