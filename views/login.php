<?php
// Tệp này chỉ chứa nội dung HTML/PHP logic của form, được nhúng vào index.php
$error = $_GET['error'] ?? null;
$success = $_GET['success'] ?? null;
?>
<div class="auth-container row g-0 rounded-4">
    <div class="col-lg-6 col-12 auth-form-col">
        <h2 class="text-center text-primary-custom mb-4 fw-bold">Đăng nhập</h2>

        <?php
        // Hiển thị thông báo (nếu có)
        if ($success == 'registered'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">Đăng ký thành công! Vui lòng đăng
                nhập.</div>
        <?php elseif ($error == 'invalid_credentials'): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">Email hoặc mật khẩu không chính xác.
            </div>
        <?php endif; ?>

        <form action="handle/login_process.php" method="POST" class="needs-validation" novalidate>
            <div class="mb-3">
                <label for="email" class="form-label">Địa chỉ Email</label>
                <input type="email" class="form-control rounded-3" id="email" name="email" required
                    placeholder="name@example.com">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mật khẩu</label>
                <input type="password" class="form-control rounded-3" id="password" name="password" required
                    placeholder="Nhập mật khẩu">
            </div>

            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-custom w-100 btn-lg rounded-3">Đăng nhập</button>
            </div>
        </form>

        <p class="text-center mt-3 mb-0">
            Chưa có tài khoản? <a href="views/register.php" class="text-decoration-none fw-semibold">Đăng ký ngay</a>
        </p>
    </div>

    <div class="col-lg-6 d-none d-lg-block auth-image-col">
        <!-- Hình ảnh sẽ được hiển thị bằng CSS background -->
    </div>
</div>