<?php
require_once __DIR__ . '/../../functions/auth.php';
require_once __DIR__ . '/../../functions/study_plan_functions.php';
require_once __DIR__ . '/../../functions/stage_functions.php';
checkLogin(__DIR__ . '/../../index.php');
$currentUser = getCurrentUser();

// Lấy ID giai đoạn từ URL
$stageId = intval($_GET['id'] ?? 0);
if (empty($stageId)) {
    header("Location: plan_list.php?error=Không tìm thấy giai đoạn");
    exit();
}

// Lấy thông tin giai đoạn
$stage = getStageById($stageId, $currentUser['id']);
if (!$stage) {
    header("Location: plan_list.php?error=Giai đoạn không tồn tại hoặc bạn không có quyền truy cập");
    exit();
}

// Lấy thông tin kế hoạch
$plan = getStudyPlanById($stage['plan_id'], $currentUser['id']);
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
    <title>Chỉnh sửa Giai đoạn - <?php echo htmlspecialchars($stage['title']); ?></title>
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
                                    <i class="bi bi-pencil"></i> Chỉnh sửa giai đoạn
                                </li>
                            </ol>
                        </nav>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <h2 class="page-title">
                            <i class="bi bi-journal-text"></i> Chỉnh sửa Giai đoạn
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
                
                <!-- Form chỉnh sửa giai đoạn -->
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <i class="bi bi-file-earmark-text"></i> Thông tin giai đoạn
                            </div>
                            <div class="card-body">
                                <form action="../../handle/study_plan_process.php" method="POST">
                                    <input type="hidden" name="action" value="update_stage">
                                    <input type="hidden" name="stage_id" value="<?php echo $stage['id']; ?>">
                                    <input type="hidden" name="plan_id" value="<?php echo $plan['id']; ?>">
                                    
                                    <div class="mb-3">
                                        <label for="title" class="form-label">
                                            <i class="bi bi-card-heading"></i> Tiêu đề giai đoạn <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="title" name="title" required value="<?php echo htmlspecialchars($stage['title']); ?>" placeholder="Nhập tiêu đề giai đoạn">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="description" class="form-label">
                                            <i class="bi bi-card-text"></i> Mô tả
                                        </label>
                                        <textarea class="form-control" id="description" name="description" rows="4" placeholder="Nhập mô tả chi tiết về giai đoạn"><?php echo htmlspecialchars($stage['description']); ?></textarea>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="deadline" class="form-label">
                                                    <i class="bi bi-calendar-x"></i> Thời hạn
                                                </label>
                                                <input type="date" class="form-control" id="deadline" name="deadline" value="<?php echo $stage['deadline']; ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="priority" class="form-label">
                                                    <i class="bi bi-flag"></i> Mức độ ưu tiên
                                                </label>
                                                <select class="form-select" id="priority" name="priority">
                                                    <option value="low" <?php echo $stage['priority'] === 'low' ? 'selected' : ''; ?>>Thấp</option>
                                                    <option value="medium" <?php echo $stage['priority'] === 'medium' ? 'selected' : ''; ?>>Trung bình</option>
                                                    <option value="high" <?php echo $stage['priority'] === 'high' ? 'selected' : ''; ?>>Cao</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="status" class="form-label">
                                            <i class="bi bi-check-circle"></i> Trạng thái
                                        </label>
                                        <select class="form-select" id="status" name="status">
                                            <option value="not_started" <?php echo $stage['status'] === 'not_started' ? 'selected' : ''; ?>>Chưa bắt đầu</option>
                                            <option value="in_progress" <?php echo $stage['status'] === 'in_progress' ? 'selected' : ''; ?>>Đang thực hiện</option>
                                            <option value="completed" <?php echo $stage['status'] === 'completed' ? 'selected' : ''; ?>>Đã hoàn thành</option>
                                        </select>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between">
                                        <a href="view_plan.php?id=<?php echo $plan['id']; ?>" class="btn btn-secondary">
                                            <i class="bi bi-arrow-left"></i> Quay lại
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-save"></i> Cập nhật giai đoạn
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
                                    <li>Cập nhật trạng thái khi có thay đổi</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>