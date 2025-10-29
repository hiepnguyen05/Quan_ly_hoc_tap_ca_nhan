<?php
$error = $_GET['error'] ?? null;
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký Tài khoản</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/auth.css">
</head>

<body>
    <div class="auth-container row g-0 rounded-4">
        <div class="col-lg-6 col-12 auth-form-col">
            <h2 class="text-center text-primary-custom mb-4 fw-bold">Tạo Tài khoản Mới</h2>

            <?php if ($error == 'email_exists'): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Email này đã được sử dụng. Vui lòng thử email khác.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form action="../handle/register_process.php" method="POST" class="needs-validation" novalidate>
                <div class="mb-3">
                    <label for="email" class="form-label">Địa chỉ Email</label>
                    <input type="email" class="form-control rounded-3" id="email" name="email" required
                        placeholder="name@example.com">
                </div>
                <div class="mb-3">
                    <label for="fullName" class="form-label">Họ tên đầy đủ</label>
                    <input type="text" class="form-control rounded-3" id="fullName" name="full_name" required
                        placeholder="Nguyễn Văn A">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Mật khẩu</label>
                    <input type="password" class="form-control rounded-3" id="password" name="password" required
                        placeholder="Tối thiểu 6 ký tự">
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-custom w-100 btn-lg rounded-3">Đăng ký</button>
                </div>
            </form>

            <p class="text-center mt-3 mb-0">
                Đã có tài khoản? <a href="../index.php" class="text-decoration-none fw-semibold">Đăng nhập</a>
            </p>
        </div>

        <div class="col-lg-6 d-none d-lg-block auth-image-col">
            <!-- Hình ảnh sẽ được hiển thị bằng CSS background -->
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</body>

</html>