<?php
session_start();
// Đã sửa đường dẫn theo cấu trúc mới
require_once '../../functions/admin/user_functions.php';

// BẢO MẬT: Chặn truy cập nếu không phải Admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../../index.php");
    exit;
}

$users = getAllUsers();
$current_page = basename(__FILE__);

// ... (HTML và nội dung bảng tiếp tục như cũ)
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản trị | Quản lý Tài khoản</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/admin/admin.css">
</head>

<body>
    <?php include '../header.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 p-0">
                <?php include 'sidebar.php'; ?>
            </div>

            <!-- Nội dung Chính (Main Content) -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <h1 class="text-primary-custom mb-4">Quản lý Tài khoản Người dùng</h1>
                <p class="lead">Tổng số người dùng: **<?php echo count($users); ?>**</p>

                <?php
                // Hiển thị thông báo thành công hoặc lỗi từ handle/admin_process.php
                if (isset($_GET['status'])):
                    $msg = '';
                    $class = '';
                    if ($_GET['status'] == 'updated') {
                        $msg = 'Cập nhật tài khoản thành công.';
                        $class = 'alert-success';
                    }
                    if ($_GET['status'] == 'deleted') {
                        $msg = 'Xóa tài khoản thành công.';
                        $class = 'alert-warning';
                    }
                    if ($_GET['status'] == 'error') {
                        $msg = 'Có lỗi xảy ra trong quá trình xử lý.';
                        $class = 'alert-danger';
                    }
                    if ($_GET['status'] == 'error_self_delete') {
                        $msg = 'Bạn không thể tự xóa tài khoản quản trị của mình.';
                        $class = 'alert-danger';
                    }
                    if ($_GET['status'] == 'error_self_demote') {
                        $msg = 'Bạn không thể tự hạ cấp vai trò của mình.';
                        $class = 'alert-danger';
                    }
                ?>
                    <div class="alert <?php echo $class; ?> alert-dismissible fade show" role="alert">
                        <?php echo $msg; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="table-responsive mt-4">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr class="table-light">
                                <th>ID</th>
                                <th>Họ Tên</th>
                                <th>Email</th>
                                <th>Vai trò</th>
                                <th>Ngày Tạo</th>
                                <th class="text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo $user['id']; ?></td>
                                    <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <span
                                            class="badge <?php echo ($user['role'] === 'admin') ? 'bg-danger' : 'bg-primary'; ?>">
                                            <?php echo strtoupper($user['role']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                                    <td class="text-center">
                                        <!-- Nút Edit mở Modal -->
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#editModal<?php echo $user['id']; ?>">
                                            Sửa
                                        </button>
                                        <!-- Nút Delete -->
                                        <?php if ((int)$user['id'] !== (int)$_SESSION['user_id']): ?>
                                            <a href="../../handle/admin/admin_process.php?action=delete&id=<?php echo $user['id']; ?>"
                                                class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Bạn có chắc chắn muốn xóa tài khoản này?');">
                                                Xóa
                                            </a>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-secondary" disabled>Tài khoản hiện tại</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php
                                // Mã Modal chỉnh sửa được đặt ở cuối tệp
                                include 'user_edit_modal.php';
                                ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>