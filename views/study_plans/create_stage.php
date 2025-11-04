<?php
require_once __DIR__ . '/../../functions/auth.php';
require_once __DIR__ . '/../../functions/study_plan_functions.php';
checkLogin(__DIR__ . '/../../index.php');
$currentUser = getCurrentUser();

// Lấy ID kế hoạch từ URL
$planId = intval($_GET['plan_id'] ?? 0);
if (empty($planId)) {
    header("Location: plan_list.php?error=Không tìm thấy kế hoạch");
    exit();
}

// Lấy thông tin kế hoạch
$plan = getStudyPlanById($planId, $currentUser['id']);
if (!$plan) {
    header("Location: plan_list.php?error=Kế hoạch không tồn tại hoặc bạn không có quyền truy cập");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Giai đoạn - <?php echo htmlspecialchars($plan['title']); ?></title>
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
                                <li class="breadcrumb-item">
                                    <a href="view_plan.php?id=<?php echo $plan['id']; ?>">
                                        <?php echo htmlspecialchars($plan['title']); ?>
                                    </a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    <i class="bi bi-plus-circle"></i> Thêm giai đoạn
                                </li>
                            </ol>
                        </nav>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <h2 class="page-title">
                            <i class="bi bi-journal-plus"></i> Thêm Giai đoạn Mới
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
                
                <!-- Form tạo giai đoạn -->
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <i class="bi bi-file-earmark-text"></i> Thông tin giai đoạn
                            </div>
                            <div class="card-body">
                                <form action="../../handle/study_plan_process.php" method="POST">
                                    <input type="hidden" name="action" value="create_stage">
                                    <input type="hidden" name="plan_id" value="<?php echo $plan['id']; ?>">
                                    
                                    <div class="mb-3">
                                        <label for="title" class="form-label">
                                            <i class="bi bi-card-heading"></i> Tiêu đề giai đoạn <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="title" name="title" required placeholder="Nhập tiêu đề giai đoạn">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="description" class="form-label">
                                            <i class="bi bi-card-text"></i> Mô tả
                                        </label>
                                        <textarea class="form-control" id="description" name="description" rows="4" placeholder="Nhập mô tả chi tiết về giai đoạn"></textarea>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="deadline" class="form-label">
                                                    <i class="bi bi-calendar-x"></i> Thời hạn
                                                </label>
                                                <input type="date" class="form-control" id="deadline" name="deadline">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="priority" class="form-label">
                                                    <i class="bi bi-flag"></i> Mức độ ưu tiên
                                                </label>
                                                <select class="form-select" id="priority" name="priority">
                                                    <option value="low">Thấp</option>
                                                    <option value="medium" selected>Trung bình</option>
                                                    <option value="high">Cao</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between">
                                        <a href="view_plan.php?id=<?php echo $plan['id']; ?>" class="btn btn-secondary">
                                            <i class="bi bi-arrow-left"></i> Quay lại
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-save"></i> Thêm giai đoạn
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <i class="bi bi-info-circle"></i> Thông tin kế hoạch
                            </div>
                            <div class="card-body">
                                <h5><i class="bi bi-journal-text"></i> <?php echo htmlspecialchars($plan['title']); ?></h5>
                                <?php if (!empty($plan['description'])): ?>
                                <p class="small text-muted"><?php echo htmlspecialchars(substr($plan['description'], 0, 100)); ?><?php echo strlen($plan['description']) > 100 ? '...' : ''; ?></p>
                                <?php endif; ?>
                                
                                <hr>
                                
                                <h5><i class="bi bi-lightbulb"></i> Mẹo:</h5>
                                <ul>
                                    <li>Đặt tiêu đề ngắn gọn, dễ hiểu</li>
                                    <li>Thêm mô tả chi tiết để nhớ rõ mục tiêu</li>
                                    <li>Thiết lập thời hạn hợp lý</li>
                                    <li>Chọn mức độ ưu tiên phù hợp</li>
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
        // Đặt ngày mặc định cho trường deadline
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date();
            today.setDate(today.getDate() + 7);
            document.getElementById('deadline').value = today.toISOString().split('T')[0];
        });
    </script>
</body>

</html>