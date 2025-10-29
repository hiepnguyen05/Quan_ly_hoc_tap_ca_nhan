<?php
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../views/login.php');
    exit;
}

require_once '../../functions/study_management/assignment_functions.php';
require_once '../../functions/study_management/subject_functions.php';

// Xử lý các hành động liên quan đến bài tập
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $userId = $_SESSION['user_id'];
    
    switch ($action) {
        case 'create':
            $subjectId = $_POST['subject_id'] ?? '';
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $dueDate = $_POST['due_date'] ?? '';
            
            // Kiểm tra môn học có thuộc về người dùng không
            $subject = getSubjectById($subjectId, $userId);
            if (!$subject) {
                $_SESSION['error'] = 'Môn học không hợp lệ';
                header('Location: ../../views/study_management/assignments.php');
                exit;
            }
            
            if (empty($title)) {
                $_SESSION['error'] = 'Vui lòng nhập tiêu đề bài tập';
                header('Location: ../../views/study_management/assignments.php');
                exit;
            }
            
            $assignmentId = createAssignment($userId, $subjectId, $title, $description, $dueDate);
            if ($assignmentId) {
                $_SESSION['success'] = 'Bài tập đã được tạo thành công';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra khi tạo bài tập';
            }
            
            header('Location: ../../views/study_management/assignments.php');
            exit;
            
        case 'update':
            $assignmentId = $_POST['assignment_id'] ?? '';
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $dueDate = $_POST['due_date'] ?? '';
            
            // Kiểm tra bài tập có thuộc về người dùng không
            $assignment = getAssignmentById($assignmentId, $userId);
            if (!$assignment) {
                $_SESSION['error'] = 'Bài tập không hợp lệ';
                header('Location: ../../views/study_management/assignments.php');
                exit;
            }
            
            if (empty($title)) {
                $_SESSION['error'] = 'Vui lòng nhập tiêu đề bài tập';
                header('Location: ../../views/study_management/assignments.php');
                exit;
            }
            
            $success = updateAssignment($assignmentId, $userId, $title, $description, $dueDate);
            if ($success) {
                $_SESSION['success'] = 'Bài tập đã được cập nhật thành công';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật bài tập';
            }
            
            header('Location: ../../views/study_management/assignments.php');
            exit;
            
        case 'update_status':
            $assignmentId = $_POST['assignment_id'] ?? '';
            $status = $_POST['status'] ?? 'pending';
            
            // Kiểm tra bài tập có thuộc về người dùng không
            $assignment = getAssignmentById($assignmentId, $userId);
            if (!$assignment) {
                $_SESSION['error'] = 'Bài tập không hợp lệ';
                header('Location: ../../views/study_management/assignments.php');
                exit;
            }
            
            $success = updateAssignmentStatus($assignmentId, $userId, $status);
            if ($success) {
                $_SESSION['success'] = 'Trạng thái bài tập đã được cập nhật';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật trạng thái bài tập';
            }
            
            header('Location: ../../views/study_management/assignments.php');
            exit;
            
        case 'delete':
            $assignmentId = $_POST['assignment_id'] ?? '';
            
            // Kiểm tra bài tập có thuộc về người dùng không
            $assignment = getAssignmentById($assignmentId, $userId);
            if (!$assignment) {
                $_SESSION['error'] = 'Bài tập không hợp lệ';
                header('Location: ../../views/study_management/assignments.php');
                exit;
            }
            
            $success = deleteAssignment($assignmentId, $userId);
            if ($success) {
                $_SESSION['success'] = 'Bài tập đã được xóa thành công';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra khi xóa bài tập';
            }
            
            header('Location: ../../views/study_management/assignments.php');
            exit;
    }
}

// Nếu không phải POST request, chuyển hướng về trang danh sách bài tập
header('Location: ../../views/study_management/assignments.php');
exit;
?>