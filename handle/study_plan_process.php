<?php
session_start();
require_once '../functions/auth.php';
require_once '../functions/study_plan_functions.php';
require_once '../functions/stage_functions.php';

// Kiểm tra đăng nhập
if (!isLoggedIn()) {
    $_SESSION['error'] = 'Bạn cần đăng nhập để thực hiện thao tác này!';
    header('Location: ../index.php');
    exit();
}

$currentUser = getCurrentUser();
$userId = $currentUser['id'];

// Kiểm tra action được truyền qua URL hoặc POST
$action = '';
if (isset($_GET['action'])) {
    $action = $_GET['action'];
} elseif (isset($_POST['action'])) {
    $action = $_POST['action'];
}

switch ($action) {
    case 'create':
        handleCreatePlan($userId);
        break;
    case 'edit':
        handleEditPlan($userId);
        break;
    case 'delete':
        handleDeletePlan($userId);
        break;
    case 'create_stage':
        handleCreateStage($userId);
        break;
    case 'update_stage':
        handleUpdateStage($userId);
        break;
    case 'update_stage_status':
        handleUpdateStageStatus($userId);
        break;
    case 'delete_stage':
        handleDeleteStage($userId);
        break;
    default:
        // Nếu không có action hợp lệ, chuyển hướng về trang danh sách kế hoạch
        header('Location: ../views/study_plans/plan_list.php');
        exit();
}

/**
 * Xử lý tạo kế hoạch học tập mới
 */
function handleCreatePlan($userId) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/study_plans/plan_list.php?error=Phương thức không hợp lệ");
        exit();
    }
    
    // Lấy dữ liệu từ form
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $startDate = trim($_POST['start_date'] ?? '');
    $endDate = trim($_POST['end_date'] ?? '');
    
    // Kiểm tra dữ liệu đầu vào
    if (empty($title)) {
        header("Location: ../views/study_plans/create_plan.php?error=Vui lòng nhập tiêu đề kế hoạch");
        exit();
    }
    
    // Nếu có nhập ngày thì kiểm tra định dạng
    if (!empty($startDate) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) {
        header("Location: ../views/study_plans/create_plan.php?error=Ngày bắt đầu không hợp lệ");
        exit();
    }
    
    if (!empty($endDate) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) {
        header("Location: ../views/study_plans/create_plan.php?error=Ngày kết thúc không hợp lệ");
        exit();
    }
    
    // Nếu có cả ngày bắt đầu và kết thúc thì kiểm tra tính hợp lệ
    if (!empty($startDate) && !empty($endDate) && $startDate > $endDate) {
        header("Location: ../views/study_plans/create_plan.php?error=Ngày kết thúc phải sau ngày bắt đầu");
        exit();
    }
    
    // Tạo kế hoạch học tập
    $planId = createStudyPlan($userId, $title, $description, $startDate, $endDate);
    
    if ($planId) {
        header("Location: ../views/study_plans/plan_list.php?success=Kế hoạch học tập đã được tạo thành công");
        exit();
    } else {
        header("Location: ../views/study_plans/create_plan.php?error=Có lỗi xảy ra khi tạo kế hoạch. Vui lòng thử lại!");
        exit();
    }
}

/**
 * Xử lý chỉnh sửa kế hoạch học tập
 */
function handleEditPlan($userId) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/study_plans/plan_list.php?error=Phương thức không hợp lệ");
        exit();
    }
    
    // Lấy dữ liệu từ form
    $planId = intval($_POST['plan_id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $startDate = trim($_POST['start_date'] ?? '');
    $endDate = trim($_POST['end_date'] ?? '');
    
    // Kiểm tra dữ liệu đầu vào
    if (empty($planId) || empty($title)) {
        header("Location: ../views/study_plans/plan_list.php?error=Thiếu thông tin cần thiết");
        exit();
    }
    
    // Kiểm tra xem kế hoạch có thuộc về người dùng hiện tại không
    $plan = getStudyPlanById($planId, $userId);
    if (!$plan) {
        header("Location: ../views/study_plans/plan_list.php?error=Kế hoạch không tồn tại hoặc bạn không có quyền truy cập");
        exit();
    }
    
    // Nếu có nhập ngày thì kiểm tra định dạng
    if (!empty($startDate) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) {
        header("Location: ../views/study_plans/edit_plan.php?id=" . $planId . "&error=Ngày bắt đầu không hợp lệ");
        exit();
    }
    
    if (!empty($endDate) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) {
        header("Location: ../views/study_plans/edit_plan.php?id=" . $planId . "&error=Ngày kết thúc không hợp lệ");
        exit();
    }
    
    // Nếu có cả ngày bắt đầu và kết thúc thì kiểm tra tính hợp lệ
    if (!empty($startDate) && !empty($endDate) && $startDate > $endDate) {
        header("Location: ../views/study_plans/edit_plan.php?id=" . $planId . "&error=Ngày kết thúc phải sau ngày bắt đầu");
        exit();
    }
    
    // Cập nhật kế hoạch học tập
    $success = updateStudyPlan($planId, $userId, $title, $description, $startDate, $endDate);
    
    if ($success) {
        header("Location: ../views/study_plans/view_plan.php?id=" . $planId . "&success=Kế hoạch học tập đã được cập nhật thành công");
        exit();
    } else {
        header("Location: ../views/study_plans/edit_plan.php?id=" . $planId . "&error=Có lỗi xảy ra khi cập nhật kế hoạch. Vui lòng thử lại!");
        exit();
    }
}

/**
 * Xử lý xóa kế hoạch học tập
 */
function handleDeletePlan($userId) {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        header("Location: ../views/study_plans/plan_list.php?error=Phương thức không hợp lệ");
        exit();
    }
    
    $planId = intval($_GET['id'] ?? 0);
    
    // Kiểm tra dữ liệu đầu vào
    if (empty($planId)) {
        header("Location: ../views/study_plans/plan_list.php?error=Không tìm thấy ID kế hoạch");
        exit();
    }
    
    // Kiểm tra xem kế hoạch có thuộc về người dùng hiện tại không
    $plan = getStudyPlanById($planId, $userId);
    if (!$plan) {
        header("Location: ../views/study_plans/plan_list.php?error=Kế hoạch không tồn tại hoặc bạn không có quyền truy cập");
        exit();
    }
    
    // Xóa kế hoạch học tập
    $success = deleteStudyPlan($planId, $userId);
    
    if ($success) {
        header("Location: ../views/study_plans/plan_list.php?success=Kế hoạch học tập đã được xóa thành công");
        exit();
    } else {
        header("Location: ../views/study_plans/plan_list.php?error=Có lỗi xảy ra khi xóa kế hoạch. Vui lòng thử lại!");
        exit();
    }
}

/**
 * Xử lý tạo giai đoạn mới cho kế hoạch
 */
function handleCreateStage($userId) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/study_plans/plan_list.php?error=Phương thức không hợp lệ");
        exit();
    }
    
    // Lấy dữ liệu từ form
    $planId = intval($_POST['plan_id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $deadline = trim($_POST['deadline'] ?? '');
    $priority = trim($_POST['priority'] ?? 'medium');
    
    // Kiểm tra dữ liệu đầu vào
    if (empty($planId) || empty($title)) {
        header("Location: ../views/study_plans/view_plan.php?id=" . $planId . "&error=Vui lòng nhập tiêu đề giai đoạn");
        exit();
    }
    
    // Kiểm tra xem kế hoạch có thuộc về người dùng hiện tại không
    $plan = getStudyPlanById($planId, $userId);
    if (!$plan) {
        header("Location: ../views/study_plans/plan_list.php?error=Kế hoạch không tồn tại hoặc bạn không có quyền truy cập");
        exit();
    }
    
    // Nếu không có deadline được nhập, tự động tính từ giai đoạn trước
    if (empty($deadline)) {
        // Lấy giai đoạn cuối cùng của kế hoạch
        $lastStage = getLastStageOfPlan($planId);
        
        if ($lastStage && !empty($lastStage['deadline'])) {
            // Tính deadline mới: deadline của giai đoạn trước + 7 ngày
            $deadlineDate = new DateTime($lastStage['deadline']);
            $deadlineDate->modify('+7 days');
            $deadline = $deadlineDate->format('Y-m-d');
        } else {
            // Nếu không có giai đoạn trước hoặc không có deadline, dùng ngày hiện tại + 7 ngày
            $deadlineDate = new DateTime();
            $deadlineDate->modify('+7 days');
            $deadline = $deadlineDate->format('Y-m-d');
        }
    }
    
    // Kiểm tra định dạng ngày
    if (!empty($deadline) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $deadline)) {
        header("Location: ../views/study_plans/create_stage.php?plan_id=" . $planId . "&error=Ngày deadline không hợp lệ");
        exit();
    }
    
    // Kiểm tra ngày deadline không được vượt quá ngày kết thúc của kế hoạch học tập
    if (!empty($deadline) && !empty($plan['end_date']) && $deadline > $plan['end_date']) {
        header("Location: ../views/study_plans/create_stage.php?plan_id=" . $planId . "&error=Ngày hết hạn của giai đoạn không được vượt quá ngày kết thúc của kế hoạch học tập (" . date('d/m/Y', strtotime($plan['end_date'])) . ")");
        exit();
    }
    
    // Tạo giai đoạn mới
    $stageId = createPlanStage($planId, $title, $description, $deadline, $priority);
    
    if ($stageId) {
        header("Location: ../views/study_plans/view_plan.php?id=" . $planId . "&success=Giai đoạn đã được thêm thành công");
        exit();
    } else {
        header("Location: ../views/study_plans/create_stage.php?plan_id=" . $planId . "&error=Có lỗi xảy ra khi thêm giai đoạn. Vui lòng thử lại!");
        exit();
    }
}

/**
 * Xử lý cập nhật giai đoạn
 */
function handleUpdateStage($userId) {
    // Kiểm tra phương thức POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $_SESSION['error'] = 'Yêu cầu không hợp lệ!';
        header('Location: ../views/study_plans/plan_list.php');
        exit();
    }
    
    // Lấy dữ liệu từ form
    $stageId = intval($_POST['stage_id'] ?? 0);
    $planId = intval($_POST['plan_id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $deadline = trim($_POST['deadline'] ?? '');
    $priority = trim($_POST['priority'] ?? 'medium');
    $status = trim($_POST['status'] ?? 'not_started');
    
    // Kiểm tra dữ liệu đầu vào
    if (empty($stageId) || empty($title)) {
        $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin bắt buộc!';
        if (!empty($planId)) {
            header('Location: ../views/study_plans/edit_stage.php?id=' . $stageId . '&error=' . urlencode('Vui lòng điền đầy đủ thông tin bắt buộc!'));
        } else {
            header('Location: ../views/study_plans/plan_list.php?error=' . urlencode('Vui lòng điền đầy đủ thông tin bắt buộc!'));
        }
        exit();
    }
    
    // Kiểm tra xem kế hoạch có thuộc về người dùng hiện tại không
    $plan = getStudyPlanById($planId, $userId);
    if (!$plan) {
        $_SESSION['error'] = 'Kế hoạch không tồn tại hoặc bạn không có quyền truy cập!';
        header('Location: ../views/study_plans/plan_list.php?error=' . urlencode('Kế hoạch không tồn tại hoặc bạn không có quyền truy cập!'));
        exit();
    }
    
    // Kiểm tra ngày deadline không được vượt quá ngày kết thúc của kế hoạch học tập
    if (!empty($deadline) && !empty($plan['end_date']) && $deadline > $plan['end_date']) {
        $_SESSION['error'] = 'Ngày hết hạn của giai đoạn không được vượt quá ngày kết thúc của kế hoạch học tập (' . date('d/m/Y', strtotime($plan['end_date'])) . ')';
        header('Location: ../views/study_plans/edit_stage.php?id=' . $stageId . '&error=' . urlencode('Ngày hết hạn của giai đoạn không được vượt quá ngày kết thúc của kế hoạch học tập (' . date('d/m/Y', strtotime($plan['end_date'])) . ')'));
        exit();
    }
    
    // Cập nhật giai đoạn
    $result = updateStage($stageId, $userId, $title, $description, $deadline, $priority);
    
    if ($result !== false) {
        // Cập nhật trạng thái nếu có
        if (!empty($status)) {
            updateStageStatus($stageId, $userId, $status);
        }
        
        $_SESSION['success'] = 'Giai đoạn đã được cập nhật thành công!';
        if (!empty($planId)) {
            header('Location: ../views/study_plans/view_plan.php?id=' . $planId . '&success=' . urlencode('Giai đoạn đã được cập nhật thành công!'));
        } else {
            header('Location: ../views/study_plans/plan_list.php?success=' . urlencode('Giai đoạn đã được cập nhật thành công!'));
        }
        exit();
    } else {
        $_SESSION['error'] = 'Không thể cập nhật giai đoạn. Vui lòng thử lại!';
        if (!empty($planId)) {
            header('Location: ../views/study_plans/edit_stage.php?id=' . $stageId . '&error=' . urlencode('Không thể cập nhật giai đoạn. Vui lòng thử lại!'));
        } else {
            header('Location: ../views/study_plans/plan_list.php?error=' . urlencode('Không thể cập nhật giai đoạn. Vui lòng thử lại!'));
        }
        exit();
    }
}

/**
 * Xử lý cập nhật trạng thái giai đoạn
 */
function handleUpdateStageStatus($userId) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/study_plans/plan_list.php?error=Phương thức không hợp lệ");
        exit();
    }
    
    // Lấy dữ liệu từ form
    $stageId = intval($_POST['stage_id'] ?? 0);
    $planId = intval($_POST['plan_id'] ?? 0);
    $status = trim($_POST['status'] ?? '');
    
    // Kiểm tra dữ liệu đầu vào
    if (empty($stageId) || empty($planId) || empty($status)) {
        header("Location: ../views/study_plans/view_plan.php?id=" . $planId . "&error=Thiếu thông tin cần thiết");
        exit();
    }
    
    // Kiểm tra xem giai đoạn có thuộc về người dùng hiện tại không
    $stage = getStageById($stageId, $userId);
    if (!$stage) {
        header("Location: ../views/study_plans/plan_list.php?error=Giai đoạn không tồn tại hoặc bạn không có quyền truy cập");
        exit();
    }
    
    // Kiểm tra trạng thái hợp lệ
    $validStatuses = ['not_started', 'in_progress', 'completed'];
    if (!in_array($status, $validStatuses)) {
        header("Location: ../views/study_plans/view_plan.php?id=" . $planId . "&error=Trạng thái không hợp lệ");
        exit();
    }
    
    // Cập nhật trạng thái
    $success = updateStageStatus($stageId, $userId, $status);
    
    if ($success) {
        $statusText = '';
        switch ($status) {
            case 'not_started':
                $statusText = 'chưa bắt đầu';
                break;
            case 'in_progress':
                $statusText = 'đang thực hiện';
                break;
            case 'completed':
                $statusText = 'đã hoàn thành';
                break;
        }
        header("Location: ../views/study_plans/view_plan.php?id=" . $planId . "&success=Trạng thái giai đoạn đã được cập nhật thành " . $statusText);
        exit();
    } else {
        header("Location: ../views/study_plans/view_plan.php?id=" . $planId . "&error=Có lỗi xảy ra khi cập nhật trạng thái giai đoạn. Vui lòng thử lại!");
        exit();
    }
}

/**
 * Xử lý xóa giai đoạn
 */
function handleDeleteStage($userId) {
    // Lấy ID giai đoạn từ URL
    $stageId = intval($_GET['id'] ?? 0);
    if (empty($stageId)) {
        $_SESSION['error'] = 'Không tìm thấy giai đoạn để xóa!';
        header('Location: ../views/study_plans/plan_list.php');
        exit();
    }
    
    // Xóa giai đoạn
    $result = deleteStage($stageId, $userId);
    
    if ($result) {
        $_SESSION['success'] = 'Giai đoạn đã được xóa thành công!';
        // Chuyển hướng về trang kế hoạch nếu có plan_id trong URL
        $planId = intval($_GET['plan_id'] ?? 0);
        if (!empty($planId)) {
            header('Location: ../views/study_plans/view_plan.php?id=' . $planId . '&success=' . urlencode('Giai đoạn đã được xóa thành công!'));
        } else {
            header('Location: ../views/study_plans/plan_list.php?success=' . urlencode('Giai đoạn đã được xóa thành công!'));
        }
        exit();
    } else {
        $_SESSION['error'] = 'Không thể xóa giai đoạn. Vui lòng thử lại!';
        header('Location: ../views/study_plans/plan_list.php');
        exit();
    }
}
