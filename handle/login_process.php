<?php
session_start();
require_once '../functions/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    // Kiểm tra dữ liệu đầu vào
    if (empty($username) || empty($password)) {
        $_SESSION['error'] = 'Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu!';
        header("Location: ../index.php");
        exit();
    }
    
    // Kết nối database
    $conn = getDbConnection();
    
    // Truy vấn người dùng
    $sql = "SELECT id, username, password, full_name, role FROM users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($user = mysqli_fetch_assoc($result)) {
            // Kiểm tra mật khẩu
            if ($password === $user['password']) { // Trong thực tế nên dùng password_verify()
                // Đăng nhập thành công
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];
                
                // Chuyển hướng đến dashboard hoặc trang quản trị tùy theo vai trò
                if ($user['role'] === 'admin') {
                    header("Location: ../views/admin/admin_dashboard.php");
                } else {
                    header("Location: ../views/dashboard.php");
                }
                exit();
            } else {
                $_SESSION['error'] = 'Mật khẩu không đúng!';
                header("Location: ../index.php");
                exit();
            }
        } else {
            $_SESSION['error'] = 'Tên đăng nhập không tồn tại!';
            header("Location: ../index.php");
            exit();
        }
        
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['error'] = 'Có lỗi xảy ra trong quá trình xử lý. Vui lòng thử lại!';
        header("Location: ../index.php");
        exit();
    }
    
    mysqli_close($conn);
} else {
    // Nếu không phải POST request, chuyển hướng về trang đăng nhập
    header("Location: ../index.php");
    exit();
}
?>