<?php
session_start();
require_once '../functions/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    handleRegister();
}

function handleRegister() {
    $conn = getDbConnection();
    
    // Lấy dữ liệu từ form (chỉ cần username và password)
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Kiểm tra dữ liệu đầu vào
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $_SESSION['register_error'] = 'Vui lòng điền đầy đủ thông tin!';
        header('Location: ../index.php');
        exit();
    }
    
    // Kiểm tra mật khẩu và xác nhận mật khẩu
    if ($password !== $confirm_password) {
        $_SESSION['register_error'] = 'Mật khẩu và xác nhận mật khẩu không khớp!';
        header('Location: ../index.php');
        exit();
    }
    
    // Kiểm tra độ dài mật khẩu
    if (strlen($password) < 6) {
        $_SESSION['register_error'] = 'Mật khẩu phải có ít nhất 6 ký tự!';
        header('Location: ../index.php');
        exit();
    }
    
    // Kiểm tra username đã tồn tại chưa
    if (isUsernameExists($conn, $username)) {
        $_SESSION['register_error'] = 'Tên đăng nhập đã tồn tại!';
        header('Location: ../index.php');
        exit();
    }
    
    // Thêm người dùng mới vào database (sử dụng username làm cả full_name)
    if (addUser($conn, $username, $username, '', $password)) {
        $_SESSION['success'] = 'Đăng ký thành công! Bạn có thể đăng nhập ngay bây giờ.';
        header('Location: ../index.php');
        exit();
    } else {
        $_SESSION['register_error'] = 'Có lỗi xảy ra khi đăng ký. Vui lòng thử lại!';
        header('Location: ../index.php');
        exit();
    }
}

function isUsernameExists($conn, $username) {
    $sql = "SELECT id FROM users WHERE username = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $exists = mysqli_num_rows($result) > 0;
        mysqli_stmt_close($stmt);
        return $exists;
    }
    return false;
}

function addUser($conn, $username, $full_name, $email, $password) {
    // Lưu mật khẩu dưới dạng plain text (trong thực tế nên dùng password_hash)
    $sql = "INSERT INTO users (username, password, email, full_name) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        // Tạo các biến riêng biệt để tránh lỗi truyền tham chiếu
        $user = $username;
        $pass = $password;
        $mail = $email;
        $name = $full_name;
        
        mysqli_stmt_bind_param($stmt, "ssss", $user, $pass, $mail, $name);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $success;
    }
    
    return false;
}
?>