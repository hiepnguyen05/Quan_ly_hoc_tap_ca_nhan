<?php
session_start();

$base_dir = '/Baitaplon/';

if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'admin') {
    header("Location: " . $base_dir . "views/admin/index.php");
    exit;
}

// Nếu người dùng đã đăng nhập, chuyển hướng đến dashboard quản lý học tập
if (isset($_SESSION['user_id'])) {
    header("Location: " . $base_dir . "views/study_management/dashboard.php");
    exit;
}

$css_file = isset($_SESSION['user_id']) ? 'css/main.css' : 'css/auth.css';
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Học Tập Cá Nhân</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?php echo $base_dir . $css_file; ?>">
</head>

<body>
    <?php
    include 'views/header.php';

    if (isset($_SESSION['user_id'])) {
        // Người dùng đã đăng nhập sẽ được chuyển hướng đến dashboard ở trên
    } else {
        include 'views/login.php';
    }
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>