<?php
session_start();
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Kế hoạch Học tập Cá nhân</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="./css/login.css?v=1.0.1" rel="stylesheet">
</head>

<body>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-8">
                <div class="login-container">
                    <!-- Phần form đăng nhập/đăng ký -->
                    <div class="login-form-wrapper">
                        <div class="form-content">
                            <div class="logo-container">
                                <i class="bi bi-journal-bookmark"></i>
                            </div>

                            <div class="text-center mb-4">
                                <h3>StudyHub</h3>
                                <p>Hệ thống quản lý kế hoạch học tập cá nhân</p>
                            </div>

                            <!-- Form đăng nhập -->
                            <form action="./handle/login_process.php" method="POST" class="login-form">
                                <!-- Thông báo lỗi -->
                                <?php if (isset($_SESSION['error'])): ?>
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <i class="bi bi-exclamation-triangle"></i>
                                        <?php
                                        echo $_SESSION['error'];
                                        unset($_SESSION['error']);
                                        ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                <?php endif; ?>

                                <!-- Thông báo thành công -->
                                <?php if (isset($_SESSION['success'])): ?>
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <i class="bi bi-check-circle"></i>
                                        <?php
                                        echo $_SESSION['success'];
                                        unset($_SESSION['success']);
                                        ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                <?php endif; ?>

                                <div class="mb-3">
                                    <label for="username" class="form-label">Tên đăng nhập</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                                        <input type="text" class="form-control" id="username" name="username"
                                            placeholder="Nhập tên đăng nhập" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">Mật khẩu</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                        <input type="password" class="form-control" id="password" name="password"
                                            placeholder="Nhập mật khẩu" required>
                                    </div>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" name="login" class="btn btn-login">
                                        <i class="bi bi-box-arrow-in-right"></i> Đăng nhập
                                    </button>
                                </div>

                                <div class="text-center">
                                    <p class="mb-0">Chưa có tài khoản? <a href="#" class="text-decoration-none"
                                            id="show-register">Đăng ký ngay</a></p>
                                </div>
                            </form>

                            <!-- Form đăng ký (ẩn ban đầu) -->
                            <form action="./handle/register_process.php" method="POST" class="register-form d-none">
                                <h4 class="text-center mb-4">
                                    <i class="bi bi-person-plus"></i> Tạo tài khoản mới
                                </h4>

                                <!-- Thông báo lỗi đăng ký -->
                                <?php if (isset($_SESSION['register_error'])): ?>
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <i class="bi bi-exclamation-triangle"></i>
                                        <?php
                                        echo $_SESSION['register_error'];
                                        unset($_SESSION['register_error']);
                                        ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                <?php endif; ?>

                                <div class="mb-3">
                                    <label for="reg_username" class="form-label">Tên đăng nhập</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                                        <input type="text" class="form-control" id="reg_username" name="username"
                                            placeholder="Nhập tên đăng nhập" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="reg_password" class="form-label">Mật khẩu</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                        <input type="password" class="form-control" id="reg_password" name="password"
                                            placeholder="Nhập mật khẩu" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="reg_confirm_password" class="form-label">Xác nhận mật khẩu</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                        <input type="password" class="form-control" id="reg_confirm_password"
                                            name="confirm_password" placeholder="Nhập lại mật khẩu" required>
                                    </div>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" name="register" class="btn btn-register">
                                        <i class="bi bi-person-check"></i> Đăng ký
                                    </button>
                                </div>

                                <div class="text-center">
                                    <p class="mb-0">Đã có tài khoản? <a href="#" class="text-decoration-none"
                                            id="show-login">Đăng nhập</a></p>
                                </div>
                            </form>

                            <div class="login-footer">
                                <p class="mb-0">&copy; 2025 Hệ thống Quản lý Kế hoạch Học tập Cá nhân</p>
                            </div>
                        </div>
                    </div>

                    <!-- Phần hình ảnh -->
                    <div class="image-wrapper">
                        <img src="./images/learning-management-system.webp" alt="Hệ thống quản lý học tập"
                            class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Chuyển đổi giữa form đăng nhập và đăng ký
        document.getElementById('show-register').addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelector('.login-form').classList.add('d-none');
            document.querySelector('.register-form').classList.remove('d-none');
        });

        document.getElementById('show-login').addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelector('.register-form').classList.add('d-none');
            document.querySelector('.login-form').classList.remove('d-none');
        });
    </script>
</body>

</html>