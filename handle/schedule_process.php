<?php
session_start();
require_once '../functions/auth.php';
require_once '../functions/schedule_functions.php';

// Thêm logging để debug
error_log("=== Bắt đầu xử lý schedule_process.php ===");
error_log("Method: " . $_SERVER['REQUEST_METHOD']);
error_log("Action: " . ($_GET['action'] ?? 'none'));
error_log("Session data: " . print_r($_SESSION, true));

// Kiểm tra đăng nhập
checkLogin('../index.php');
$currentUser = getCurrentUser();

error_log("Current user: " . print_r($currentUser, true));

// Xử lý các action liên quan đến thời khóa biểu
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    error_log("Xử lý action: $action");
    
    switch ($action) {
        case 'add_schedule':
            // Thêm thời khóa biểu mới
            error_log("Xử lý thêm thời khóa biểu mới");
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                error_log("Dữ liệu POST: " . print_r($_POST, true));
                
                $scheduleName = $_POST['schedule_name'] ?? '';
                $startDate = $_POST['start_date'] ?? null;
                $endDate = $_POST['end_date'] ?? null;
                $isActive = $_POST['is_active'] ?? 1;
                $scheduleItems = $_POST['schedule_items'] ?? [];
                
                error_log("Dữ liệu đầu vào: name=$scheduleName, start=$startDate, end=$endDate, active=$isActive");
                
                if ($scheduleName) {
                    $result = createSchedule($currentUser['id'], $scheduleName, $startDate, $endDate, $isActive);
                    error_log("Kết quả tạo thời khóa biểu: " . ($result ? "Thành công - ID: $result" : "Thất bại"));
                    
                    if ($result) {
                        // Add schedule items
                        if (!empty($scheduleItems)) {
                            foreach ($scheduleItems as $day => $timeSlots) {
                                foreach ($timeSlots as $timeSlot => $studyPlanId) {
                                    if ($studyPlanId) { // Only add if a study plan was selected
                                        addScheduleItem($result, $studyPlanId, $day, $timeSlot);
                                    }
                                }
                            }
                        }
                        
                        $_SESSION['success_message'] = 'Thời khóa biểu đã được tạo thành công!';
                    } else {
                        $_SESSION['error_message'] = 'Có lỗi xảy ra khi tạo thời khóa biểu. Vui lòng kiểm tra lại thông tin và thử lại.';
                    }
                } else {
                    $_SESSION['error_message'] = 'Vui lòng nhập tên thời khóa biểu.';
                    error_log("Lỗi: Tên thời khóa biểu trống");
                }
            }
            error_log("Redirect đến schedule.php");
            header('Location: ../views/schedule/schedule.php');
            exit();
            
        case 'edit_schedule':
            // Cập nhật thời khóa biểu
            error_log("Xử lý cập nhật thời khóa biểu");
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                error_log("Dữ liệu POST: " . print_r($_POST, true));
                
                $scheduleId = $_POST['schedule_id'] ?? 0;
                $scheduleName = $_POST['schedule_name'] ?? '';
                $startDate = $_POST['start_date'] ?? null;
                $endDate = $_POST['end_date'] ?? null;
                $isActive = $_POST['is_active'] ?? 1;
                $scheduleItems = $_POST['schedule_items'] ?? [];
                
                error_log("Dữ liệu đầu vào: id=$scheduleId, name=$scheduleName, start=$startDate, end=$endDate, active=$isActive");
                
                if ($scheduleId && $scheduleName) {
                    // Cập nhật thông tin thời khóa biểu
                    $result = updateSchedule($scheduleId, $currentUser['id'], $scheduleName, $startDate, $endDate, $isActive);
                    error_log("Kết quả cập nhật thời khóa biểu: " . ($result ? "Thành công" : "Thất bại"));
                    
                    if ($result) {
                        // Xóa tất cả các môn học hiện tại
                        deleteAllScheduleItems($scheduleId, $currentUser['id']);
                        
                        // Thêm lại các môn học mới
                        if (!empty($scheduleItems)) {
                            foreach ($scheduleItems as $day => $timeSlots) {
                                foreach ($timeSlots as $timeSlot => $studyPlanId) {
                                    if ($studyPlanId) { // Only add if a study plan was selected
                                        $itemResult = addScheduleItem($scheduleId, $studyPlanId, $day, $timeSlot);
                                        if (!$itemResult) {
                                            error_log("Lỗi khi thêm môn học: day=$day, timeSlot=$timeSlot, studyPlanId=$studyPlanId");
                                        }
                                    }
                                }
                            }
                        }
                        
                        $_SESSION['success_message'] = 'Thời khóa biểu đã được cập nhật thành công!';
                    } else {
                        $_SESSION['error_message'] = 'Có lỗi xảy ra khi cập nhật thời khóa biểu. Có thể thời khóa biểu không tồn tại hoặc không thuộc quyền của bạn.';
                    }
                } else {
                    $_SESSION['error_message'] = 'Vui lòng nhập đầy đủ thông tin.';
                    error_log("Lỗi: Thiếu thông tin cần thiết");
                }
            }
            error_log("Redirect đến schedule.php");
            header('Location: ../views/schedule/schedule.php');
            exit();
            
        case 'delete_schedule':
            // Xóa thời khóa biểu
            error_log("Xử lý xóa thời khóa biểu");
            $scheduleId = $_GET['id'] ?? 0;
            error_log("ID thời khóa biểu cần xóa: $scheduleId");
            
            if ($scheduleId) {
                $result = deleteSchedule($scheduleId, $currentUser['id']);
                error_log("Kết quả xóa thời khóa biểu: " . ($result ? "Thành công" : "Thất bại"));
                
                if ($result) {
                    $_SESSION['success_message'] = 'Thời khóa biểu đã được xóa thành công!';
                } else {
                    $_SESSION['error_message'] = 'Có lỗi xảy ra khi xóa thời khóa biểu. Có thể thời khóa biểu không tồn tại hoặc không thuộc quyền của bạn.';
                }
            }
            error_log("Redirect đến schedule.php");
            header('Location: ../views/schedule/schedule.php');
            exit();
            
        case 'add_schedule_item':
            // Thêm môn học vào thời khóa biểu
            error_log("Xử lý thêm môn học vào thời khóa biểu");
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                error_log("Dữ liệu POST: " . print_r($_POST, true));
                
                $scheduleId = $_POST['schedule_id'] ?? 0;
                $studyPlanId = $_POST['study_plan_id'] ?? null;
                $dayOfWeek = $_POST['day_of_week'] ?? '';
                $timeSlot = $_POST['time_slot'] ?? '';
                
                error_log("Dữ liệu đầu vào: schedule_id=$scheduleId, study_plan_id=$studyPlanId, day=$dayOfWeek, time_slot=$timeSlot");
                
                if ($scheduleId && $studyPlanId && $dayOfWeek && $timeSlot) {
                    $result = addScheduleItem($scheduleId, $studyPlanId, $dayOfWeek, $timeSlot);
                    error_log("Kết quả thêm môn học: " . ($result ? "Thành công" : "Thất bại"));
                    
                    if ($result) {
                        $_SESSION['success_message'] = 'Môn học đã được thêm vào thời khóa biểu!';
                    } else {
                        $_SESSION['error_message'] = 'Có lỗi xảy ra khi thêm môn học. Vui lòng thử lại.';
                    }
                } else {
                    $_SESSION['error_message'] = 'Vui lòng chọn đầy đủ thông tin môn học.';
                    error_log("Lỗi: Thiếu thông tin cần thiết");
                }
            }
            $redirectId = $_POST['schedule_id'] ?? $_GET['schedule_id'] ?? 0;
            error_log("Redirect ID: $redirectId");
            
            if ($redirectId) {
                header('Location: ../views/schedule/view_schedule.php?id=' . $redirectId);
            } else {
                header('Location: ../views/schedule/schedule.php');
            }
            exit();
            
        case 'delete_schedule_item':
            // Xóa môn học khỏi thời khóa biểu
            error_log("Xử lý xóa môn học khỏi thời khóa biểu");
            $itemId = $_GET['id'] ?? 0;
            error_log("ID item cần xóa: $itemId");
            
            if ($itemId) {
                $result = deleteScheduleItem($itemId, $currentUser['id']);
                error_log("Kết quả xóa môn học: " . ($result ? "Thành công" : "Thất bại"));
                
                if ($result) {
                    $_SESSION['success_message'] = 'Môn học đã được xóa khỏi thời khóa biểu!';
                } else {
                    $_SESSION['error_message'] = 'Có lỗi xảy ra khi xóa môn học. Có thể môn học không tồn tại hoặc không thuộc quyền của bạn.';
                }
            }
            $scheduleId = $_GET['schedule_id'] ?? 0;
            error_log("Schedule ID: $scheduleId");
            
            if ($scheduleId) {
                header('Location: ../views/schedule/view_schedule.php?id=' . $scheduleId);
            } else {
                header('Location: ../views/schedule/schedule.php');
            }
            exit();
            
        default:
            error_log("Action không hợp lệ: $action");
            header('Location: ../views/schedule/schedule.php');
            exit();
    }
} else {
    // Mặc định hiển thị danh sách thời khóa biểu
    error_log("Không có action, redirect đến schedule.php");
    header('Location: ../views/schedule/schedule.php');
    exit();
}