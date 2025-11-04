<?php
session_start();
require_once '../functions/auth.php';
require_once '../functions/study_plan_functions.php';

// Kiểm tra đăng nhập
if (!isLoggedIn()) {
    $_SESSION['error'] = 'Bạn cần đăng nhập để thực hiện thao tác này!';
    header('Location: ../index.php');
    exit();
}

$currentUser = getCurrentUser();

// Kiểm tra xem người dùng có phải là admin không
if (!isset($currentUser['role']) || $currentUser['role'] !== 'admin') {
    $_SESSION['error'] = 'Bạn không có quyền thực hiện thao tác này!';
    header("Location: ../views/dashboard.php");
    exit();
}

// Kiểm tra action được truyền qua URL hoặc POST
$action = '';
if (isset($_GET['action'])) {
    $action = $_GET['action'];
} elseif (isset($_POST['action'])) {
    $action = $_POST['action'];
}

switch ($action) {
    case 'add_user':
        handleAddUser();
        break;
    case 'edit_user':
        handleEditUser();
        break;
    case 'delete_user':
        handleDeleteUser();
        break;
    default:
        header("Location: ../admin_dashboard.php?error=Hành động không hợp lệ");
        exit();
}

/**
 * Xử lý thêm người dùng mới
 */
function handleAddUser() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../admin_dashboard.php?error=Phương thức không hợp lệ");
        exit();
    }
    
    // Lấy dữ liệu từ form
    $fullName = trim($_POST['full_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role = trim($_POST['role'] ?? 'user');
    
    // Kiểm tra dữ liệu đầu vào
    if (empty($fullName) || empty($username) || empty($email) || empty($password)) {
        header("Location: ../admin_dashboard.php?error=Vui lòng điền đầy đủ thông tin");
        exit();
    }
    
    // Kiểm tra định dạng email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../admin_dashboard.php?error=Email không hợp lệ");
        exit();
    }
    
    // Kiểm tra vai trò hợp lệ
    if (!in_array($role, ['user', 'admin'])) {
        header("Location: ../admin_dashboard.php?error=Vai trò không hợp lệ");
        exit();
    }
    
    // Kết nối database
    require_once '../functions/db_connection.php';
    $conn = getDbConnection();
    
    // Kiểm tra xem username đã tồn tại chưa
    $sql = "SELECT id FROM users WHERE username = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        header("Location: ../admin_dashboard.php?error=Tên đăng nhập đã tồn tại");
        exit();
    }
    mysqli_stmt_close($stmt);
    
    // Thêm người dùng mới
    $sql = "INSERT INTO users (username, password, full_name, email, role) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssss", $username, $password, $fullName, $email, $role);
        $success = mysqli_stmt_execute($stmt);
        
        if ($success) {
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            header("Location: ../admin_dashboard.php?success=Người dùng đã được thêm thành công");
            exit();
        }
    }
    
    if ($stmt) mysqli_stmt_close($stmt);
    mysqli_close($conn);
    header("Location: ../admin_dashboard.php?error=Có lỗi xảy ra khi thêm người dùng. Vui lòng thử lại!");
    exit();
}

/**
 * Xử lý chỉnh sửa người dùng
 */
function handleEditUser() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../admin_dashboard.php?error=Phương thức không hợp lệ");
        exit();
    }
    
    // Lấy dữ liệu từ form
    $userId = intval($_POST['user_id'] ?? 0);
    $fullName = trim($_POST['full_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role = trim($_POST['role'] ?? 'user');
    
    // Kiểm tra dữ liệu đầu vào
    if (empty($userId) || empty($fullName) || empty($username) || empty($email)) {
        header("Location: ../admin_dashboard.php?error=Thiếu thông tin cần thiết");
        exit();
    }
    
    // Kiểm tra định dạng email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../admin_dashboard.php?error=Email không hợp lệ");
        exit();
    }
    
    // Kiểm tra vai trò hợp lệ
    if (!in_array($role, ['user', 'admin'])) {
        header("Location: ../admin_dashboard.php?error=Vai trò không hợp lệ");
        exit();
    }
    
    // Kết nối database
    require_once '../functions/db_connection.php';
    $conn = getDbConnection();
    
    // Kiểm tra xem người dùng có tồn tại không
    $sql = "SELECT id FROM users WHERE id = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 0) {
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        header("Location: ../admin_dashboard.php?error=Người dùng không tồn tại");
        exit();
    }
    mysqli_stmt_close($stmt);
    
    // Kiểm tra xem username đã tồn tại chưa (ngoại trừ người dùng hiện tại)
    $sql = "SELECT id FROM users WHERE username = ? AND id != ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $username, $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        header("Location: ../admin_dashboard.php?error=Tên đăng nhập đã tồn tại");
        exit();
    }
    mysqli_stmt_close($stmt);
    
    // Cập nhật thông tin người dùng
    if (!empty($password)) {
        // Cập nhật cả mật khẩu
        $sql = "UPDATE users SET full_name = ?, username = ?, email = ?, role = ?, password = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssssi", $fullName, $username, $email, $role, $password, $userId);
    } else {
        // Không cập nhật mật khẩu
        $sql = "UPDATE users SET full_name = ?, username = ?, email = ?, role = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssi", $fullName, $username, $email, $role, $userId);
    }
    
    if ($stmt) {
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        
        if ($success) {
            header("Location: ../admin_dashboard.php?success=Thông tin người dùng đã được cập nhật thành công");
            exit();
        }
    }
    
    mysqli_close($conn);
    header("Location: ../admin_dashboard.php?error=Có lỗi xảy ra khi cập nhật thông tin người dùng. Vui lòng thử lại!");
    exit();
}

/**
 * Xử lý xóa người dùng
 */
function handleDeleteUser() {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        header("Location: ../admin_dashboard.php?error=Phương thức không hợp lệ");
        exit();
    }
    
    $userId = intval($_GET['id'] ?? 0);
    
    // Kiểm tra dữ liệu đầu vào
    if (empty($userId)) {
        header("Location: ../admin_dashboard.php?error=Không tìm thấy ID người dùng");
        exit();
    }
    
    // Không cho phép xóa chính mình
    global $currentUser;
    if ($userId == $currentUser['id']) {
        header("Location: ../admin_dashboard.php?error=Bạn không thể xóa chính mình");
        exit();
    }
    
    // Kết nối database
    require_once '../functions/db_connection.php';
    $conn = getDbConnection();
    
    // Kiểm tra xem người dùng có tồn tại không
    $sql = "SELECT id FROM users WHERE id = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 0) {
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        header("Location: ../admin_dashboard.php?error=Người dùng không tồn tại");
        exit();
    }
    mysqli_stmt_close($stmt);
    
    // Xóa người dùng
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    $success = mysqli_stmt_execute($stmt);
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    if ($success) {
        header("Location: ../admin_dashboard.php?success=Người dùng đã được xóa thành công");
        exit();
    } else {
        header("Location: ../admin_dashboard.php?error=Có lỗi xảy ra khi xóa người dùng. Vui lòng thử lại!");
        exit();
    }
}
?>