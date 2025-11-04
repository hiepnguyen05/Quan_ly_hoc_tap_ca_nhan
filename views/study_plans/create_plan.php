<?php
require_once __DIR__ . '/../../functions/auth.php';
checkLogin(__DIR__ . '/../../index.php');
$currentUser = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tạo Kế hoạch Học tập - Quản lý Kế hoạch Học tập Cá nhân</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="../../css/dashboard.css" rel="stylesheet">
</head>

<body>
    <?php 
    $currentPage = basename($_SERVER['PHP_SELF']);
    include '../components/header.php'; 
    ?>
    
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-none d-md-block">
                <?php include '../components/sidebar.php'; ?>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="row">
                    <div class="col-12">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="../dashboard.php">
                                        <i class="bi bi-speedometer2"></i> Dashboard
                                    </a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="plan_list.php">
                                        <i class="bi bi-journal-bookmark"></i> Kế hoạch học tập
                                    </a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    <i class="bi bi-plus-circle"></i> Tạo kế hoạch mới
                                </li>
                            </ol>
                        </nav>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <h2 class="page-title">
                            <i class="bi bi-journal-plus"></i> Tạo Kế hoạch Học tập Mới
                        </h2>
                    </div>
                </div>
                
                <!-- Thông báo -->
                <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle"></i>
                    <?php echo htmlspecialchars($_GET['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <!-- Form tạo kế hoạch -->
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <i class="bi bi-file-earmark-text"></i> Thông tin kế hoạch
                            </div>
                            <div class="card-body">
                                <form action="../../handle/study_plan_process.php" method="POST">
                                    <input type="hidden" name="action" value="create">
                                    
                                    <div class="mb-3">
                                        <label for="title" class="form-label">
                                            <i class="bi bi-card-heading"></i> Tiêu đề kế hoạch <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="title" name="title" required placeholder="Nhập tiêu đề kế hoạch học tập">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="description" class="form-label">
                                            <i class="bi bi-card-text"></i> Mô tả
                                        </label>
                                        <textarea class="form-control" id="description" name="description" rows="4" placeholder="Nhập mô tả chi tiết về kế hoạch học tập"></textarea>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="start_date" class="form-label">
                                                    <i class="bi bi-calendar-check"></i> Ngày bắt đầu
                                                </label>
                                                <input type="date" class="form-control" id="start_date" name="start_date">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="end_date" class="form-label">
                                                    <i class="bi bi-calendar-x"></i> Ngày kết thúc
                                                </label>
                                                <input type="date" class="form-control" id="end_date" name="end_date">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between">
                                        <a href="plan_list.php" class="btn btn-secondary">
                                            <i class="bi bi-arrow-left"></i> Quay lại
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-save"></i> Tạo kế hoạch
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <i class="bi bi-info-circle"></i> Hướng dẫn
                            </div>
                            <div class="card-body">
                                <h5><i class="bi bi-lightbulb"></i> Các bước tạo kế hoạch:</h5>
                                <ol>
                                    <li>Nhập tiêu đề kế hoạch</li>
                                    <li>Thêm mô tả chi tiết (nếu cần)</li>
                                    <li>Chọn ngày bắt đầu và kết thúc</li>
                                    <li>Nhấn "Tạo kế hoạch"</li>
                                </ol>
                                <hr>
                                <h5><i class="bi bi-lightning"></i> Mẹo:</h5>
                                <ul>
                                    <li>Đặt tiêu đề ngắn gọn, dễ hiểu</li>
                                    <li>Thêm mô tả chi tiết để nhớ rõ mục tiêu</li>
                                    <li>Thiết lập thời gian hợp lý</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Đặt ngày mặc định cho các trường ngày
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('start_date').value = today;
            
            // Thiết lập ngày kết thúc mặc định (7 ngày sau ngày bắt đầu)
            const endDate = new Date();
            endDate.setDate(endDate.getDate() + 7);
            document.getElementById('end_date').value = endDate.toISOString().split('T')[0];
        });
    </script>
</body>

</html>