<?php
session_start();

$base_dir = '/Baitaplon/'; 

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: " . $base_dir . "index.php");
    exit;
}

$current_page = basename(__FILE__);

// Giả lập dữ liệu thống kê
$total_users = 120;
$total_courses = 45;
$pending_tasks = 8;
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $base_dir; ?>css/admin/admin.css">
</head>

<body>
    <?php include '../header.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 col-lg-2 p-0">
                <?php include 'sidebar.php'; ?>
            </div>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <h1 class="text-primary-custom mb-4">Tổng quan Hệ thống</h1>

                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="card stat-card h-100">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title text-muted fw-bold">TỔNG NGƯỜI DÙNG</h5>
                                    <p class="card-text fs-2 fw-bold"><?php echo $total_users; ?></p>
                                </div>
                                <div class="stat-card-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor"
                                        class="bi bi-people-fill" viewBox="0 0 16 16">
                                        <path
                                            d="M7 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H7zm1.5-10a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-4">
                        <div class="card stat-card h-100">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title text-muted fw-bold">TỔNG MÔN HỌC</h5>
                                    <p class="card-text fs-2 fw-bold"><?php echo $total_courses; ?></p>
                                </div>
                                <div class="stat-card-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor"
                                        class="bi bi-journal-bookmark-fill" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd"
                                            d="M6 1h6v7a.5.5 0 0 1-.757.429L9 7.083 6.757 8.43A.5.5 0 0 1 6 8V1z" />
                                        <path
                                            d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2z" />
                                        <path
                                            d="M1 5v-.5a.5.5 0 0 1 1 0V5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1H1zm0 3v-.5a.5.5 0 0 1 1 0V8h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1H1zm0 3v-.5a.5.5 0 0 1 1 0v.5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1H1z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-4">
                        <div class="card stat-card h-100">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title text-muted fw-bold">NHIỆM VỤ CHỜ</h5>
                                    <p class="card-text fs-2 fw-bold"><?php echo $pending_tasks; ?></p>
                                </div>
                                <div class="stat-card-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor"
                                        class="bi bi-exclamation-triangle-fill" viewBox="0 0 16 16">
                                        <path
                                            d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <h2 class="h4 text-primary-custom">Hoạt động Gần đây</h2>
                    <div class="table-responsive bg-white rounded shadow-sm">
                        <table class="table table-striped table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Thời gian</th>
                                    <th>Người dùng</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Hôm nay, 09:15 AM</td>
                                    <td>ngoc.hiep@user.com</td>
                                    <td>Đã đăng ký tài khoản mới.</td>
                                </tr>
                                <tr>
                                    <td>Hôm qua, 04:30 PM</td>
                                    <td>admin@lms.com</td>
                                    <td>Đã cập nhật vai trò cho 'user_test'.</td>
                                </tr>
                                <tr>
                                    <td>Hôm qua, 11:10 AM</td>
                                    <td>thanh.tran@user.com</td>
                                    <td>Đã hoàn thành môn học "Javascript Nâng cao".</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>