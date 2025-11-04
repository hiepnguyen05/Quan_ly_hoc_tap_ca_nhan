<?php
/**
 * Hàm kiểm tra xem user đã đăng nhập chưa
 * Nếu chưa đăng nhập, chuyển hướng về trang login
 * Nếu là admin, chuyển hướng đến trang admin dashboard
 * 
 * @param string $redirectPath Đường dẫn để chuyển hướng về trang login (mặc định: '../index.php')
 */
function checkLogin($redirectPath = '../index.php') {
    // Khởi tạo session nếu chưa có
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Kiểm tra xem user đã đăng nhập chưa
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
        // Nếu chưa đăng nhập, set thông báo lỗi và chuyển hướng
        $_SESSION['error'] = 'Bạn cần đăng nhập để truy cập trang này!';
        header('Location: ' . $redirectPath);
        exit();
    }
    
    // Nếu là admin, chuyển hướng đến trang admin dashboard
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        // Kiểm tra xem trang hiện tại có phải là trang admin không
        $currentPath = $_SERVER['REQUEST_URI'];
        if (strpos($currentPath, '/admin/') === false && 
            strpos($currentPath, 'admin_dashboard.php') === false &&
            strpos($currentPath, 'user_management.php') === false &&
            strpos($currentPath, 'study_plan_reports.php') === false &&
            strpos($currentPath, 'system_settings.php') === false) {
            header('Location: ../admin/admin_dashboard.php');
            exit();
        }
    }
}

/**
 * Hàm đăng xuất user
 * Xóa tất cả session và chuyển hướng về trang login
 * 
 * @param string $redirectPath Đường dẫn để chuyển hướng sau khi logout (mặc định: '../index.php')
 */
function logout($redirectPath = '../index.php') {
    // Khởi tạo session nếu chưa có
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Hủy tất cả session
    session_unset();
    session_destroy();
    
    // Khởi tạo session mới để lưu thông báo
    session_start();
    $_SESSION['success'] = 'Đăng xuất thành công!';
    
    // Chuyển hướng về trang đăng nhập
    header('Location: ' . $redirectPath);
    exit();
}

/**
 * Hàm lấy thông tin user hiện tại
 * 
 * @return array|null Trả về thông tin user nếu đã đăng nhập, null nếu chưa đăng nhập
 */
function getCurrentUser() {
    // Khởi tạo session nếu chưa có
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'role' => $_SESSION['role'] ?? null
        ];
    }
    
    return null;
}

/**
 * Hàm lấy tất cả người dùng
 * 
 * @return array Danh sách tất cả người dùng
 */
function getAllUsers() {
    // Kết nối database
    require_once dirname(__DIR__) . '/functions/db_connection.php';
    $conn = getDbConnection();
    
    $sql = "SELECT id, username, full_name, email, role FROM users ORDER BY id ASC";
    $result = mysqli_query($conn, $sql);
    
    $users = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }
    }
    
    mysqli_close($conn);
    return $users;
}

/**
 * Hàm kiểm tra xem user đã đăng nhập chưa (không redirect)
 * 
 * @return bool True nếu đã đăng nhập, False nếu chưa
 */
function isLoggedIn() {
    // Khởi tạo session nếu chưa có
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}

/**
 * Hàm xác thực đăng nhập
 * @param mysqli $conn
 * @param string $username
 * @param string $password
 * @return array|false Trả về thông tin user nếu đúng, false nếu sai
 */
function authenticateUser($conn, $username, $password) {
    $sql = "SELECT id, username, password, role FROM users WHERE username = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) return false;
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        // Trong thực tế nên dùng password_verify nếu có mã hóa
        if ($password === $user['password']) {
            mysqli_stmt_close($stmt);
            return $user;
        }
    }
    if ($stmt) mysqli_stmt_close($stmt);
    return false;
}

?>