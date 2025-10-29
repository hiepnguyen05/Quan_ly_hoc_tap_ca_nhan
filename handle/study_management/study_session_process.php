<?php
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../views/login.php');
    exit;
}

require_once '../../functions/study_management/study_session_functions.php';
require_once '../../functions/study_management/subject_functions.php';

// Xử lý các hành động liên quan đến phiên học
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $userId = $_SESSION['user_id'];
    
    switch ($action) {
        case 'create':
            $subjectId = $_POST['subject_id'] ?? '';
            $date = $_POST['date'] ?? '';
            $duration = $_POST['duration'] ?? '';
            
            // Kiểm tra môn học có thuộc về người dùng không
            $subject = getSubjectById($subjectId, $userId);
            if (!$subject) {
                $_SESSION['error'] = 'Môn học không hợp lệ';
                header('Location: ../../views/study_management/study_sessions.php');
                exit;
            }
            
            if (empty($date) || empty($duration)) {
                $_SESSION['error'] = 'Vui lòng nhập đầy đủ thông tin';
                header('Location: ../../views/study_management/study_sessions.php');
                exit;
            }
            
            if (!is_numeric($duration) || $duration <= 0) {
                $_SESSION['error'] = 'Thời lượng phải là số dương';
                header('Location: ../../views/study_management/study_sessions.php');
                exit;
            }
            
            $sessionId = createStudySession($userId, $subjectId, $date, $duration);
            if ($sessionId) {
                $_SESSION['success'] = 'Phiên học đã được ghi lại thành công';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra khi ghi lại phiên học';
            }
            
            header('Location: ../../views/study_management/study_sessions.php');
            exit;
            
        case 'update':
            $sessionId = $_POST['session_id'] ?? '';
            $date = $_POST['date'] ?? '';
            $duration = $_POST['duration'] ?? '';
            
            // Kiểm tra phiên học có thuộc về người dùng không
            $session = getStudySessionById($sessionId, $userId);
            if (!$session) {
                $_SESSION['error'] = 'Phiên học không hợp lệ';
                header('Location: ../../views/study_management/study_sessions.php');
                exit;
            }
            
            if (empty($date) || empty($duration)) {
                $_SESSION['error'] = 'Vui lòng nhập đầy đủ thông tin';
                header('Location: ../../views/study_management/study_sessions.php');
                exit;
            }
            
            if (!is_numeric($duration) || $duration <= 0) {
                $_SESSION['error'] = 'Thời lượng phải là số dương';
                header('Location: ../../views/study_management/study_sessions.php');
                exit;
            }
            
            $success = updateStudySession($sessionId, $userId, $date, $duration);
            if ($success) {
                $_SESSION['success'] = 'Phiên học đã được cập nhật thành công';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật phiên học';
            }
            
            header('Location: ../../views/study_management/study_sessions.php');
            exit;
            
        case 'delete':
            $sessionId = $_POST['session_id'] ?? '';
            
            // Kiểm tra phiên học có thuộc về người dùng không
            $session = getStudySessionById($sessionId, $userId);
            if (!$session) {
                $_SESSION['error'] = 'Phiên học không hợp lệ';
                header('Location: ../../views/study_management/study_sessions.php');
                exit;
            }
            
            $success = deleteStudySession($sessionId, $userId);
            if ($success) {
                $_SESSION['success'] = 'Phiên học đã được xóa thành công';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra khi xóa phiên học';
            }
            
            header('Location: ../../views/study_management/study_sessions.php');
            exit;
    }
}

// Nếu không phải POST request, chuyển hướng về trang danh sách phiên học
header('Location: ../../views/study_management/study_sessions.php');
exit;
?>