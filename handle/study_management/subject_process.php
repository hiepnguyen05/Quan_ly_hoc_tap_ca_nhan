<?php
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../views/login.php');
    exit;
}

require_once '../../functions/study_management/subject_functions.php';

// Xử lý các hành động liên quan đến môn học
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $userId = $_SESSION['user_id'];
    
    switch ($action) {
        case 'create':
            $name = trim($_POST['name'] ?? '');
            
            if (empty($name)) {
                $_SESSION['error'] = 'Vui lòng nhập tên môn học';
                header('Location: ../../views/study_management/subjects.php');
                exit;
            }
            
            $subjectId = createSubject($userId, $name);
            if ($subjectId) {
                $_SESSION['success'] = 'Môn học đã được tạo thành công';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra khi tạo môn học';
            }
            
            header('Location: ../../views/study_management/subjects.php');
            exit;
            
        case 'update':
            $subjectId = $_POST['subject_id'] ?? '';
            $name = trim($_POST['name'] ?? '');
            
            if (empty($name)) {
                $_SESSION['error'] = 'Vui lòng nhập tên môn học';
                header('Location: ../../views/study_management/subjects.php');
                exit;
            }
            
            $success = updateSubject($subjectId, $userId, $name);
            if ($success) {
                $_SESSION['success'] = 'Môn học đã được cập nhật thành công';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật môn học';
            }
            
            header('Location: ../../views/study_management/subjects.php');
            exit;
            
        case 'delete':
            $subjectId = $_POST['subject_id'] ?? '';
            
            $success = deleteSubject($subjectId, $userId);
            if ($success) {
                $_SESSION['success'] = 'Môn học đã được xóa thành công';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra khi xóa môn học';
            }
            
            header('Location: ../../views/study_management/subjects.php');
            exit;
    }
}

// Nếu không phải POST request, chuyển hướng về trang danh sách môn học
header('Location: ../../views/study_management/subjects.php');
exit;
?>