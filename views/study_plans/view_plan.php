<?php
require_once __DIR__ . '/../../functions/auth.php';
require_once __DIR__ . '/../../functions/study_plan_functions.php';
require_once __DIR__ . '/../../functions/stage_functions.php';
checkLogin(__DIR__ . '/../../index.php');
$currentUser = getCurrentUser();

// Lấy ID kế hoạch từ URL
$planId = intval($_GET['id'] ?? 0);
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

// Lấy các giai đoạn của kế hoạch
$stages = getPlanStages($planId);

// Tính tiến độ
$progress = calculatePlanProgress($planId);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($plan['title']); ?> - Quản lý Kế hoạch Học tập</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="../../css/dashboard.css" rel="stylesheet">
    <style>
        .priority-badge {
            position: absolute;
            top: 10px;
            right: 10px;
        }
    </style>
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
                                    <?php echo htmlspecialchars($plan['title']); ?>
                                </li>
                            </ol>
                        </nav>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <h2 class="page-title">
                            <i class="bi bi-journal-text"></i> <?php echo htmlspecialchars($plan['title']); ?>
                        </h2>
                    </div>
                </div>
                
                <!-- Thông báo -->
                <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i>
                    <?php echo htmlspecialchars($_GET['success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle"></i>
                    <?php echo htmlspecialchars($_GET['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <!-- Thông tin kế hoạch -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span>
                                    <i class="bi bi-file-earmark-text"></i> Thông tin kế hoạch
                                </span>
                                <div>
                                    <a href="edit_plan.php?id=<?php echo $plan['id']; ?>" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i> Chỉnh sửa
                                    </a>
                                    <a href="../../handle/study_plan_process.php?action=delete&id=<?php echo $plan['id']; ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Bạn có chắc chắn muốn xóa kế hoạch này?')">
                                        <i class="bi bi-trash"></i> Xóa
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($plan['description'])): ?>
                                <div class="mb-3">
                                    <h5><i class="bi bi-card-text"></i> Mô tả:</h5>
                                    <p><?php echo nl2br(htmlspecialchars($plan['description'])); ?></p>
                                </div>
                                <?php endif; ?>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-2">
                                            <strong><i class="bi bi-calendar-check"></i> Ngày bắt đầu:</strong>
                                            <?php echo !empty($plan['start_date']) ? date('d/m/Y', strtotime($plan['start_date'])) : '<span class="text-muted">Chưa xác định</span>'; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-2">
                                            <strong><i class="bi bi-calendar-x"></i> Ngày kết thúc:</strong>
                                            <?php echo !empty($plan['end_date']) ? date('d/m/Y', strtotime($plan['end_date'])) : '<span class="text-muted">Chưa xác định</span>'; ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-2">
                                    <strong><i class="bi bi-calendar"></i> Ngày tạo:</strong>
                                    <?php echo date('d/m/Y H:i', strtotime($plan['created_at'])); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <i class="bi bi-bar-chart"></i> Tiến độ
                            </div>
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    <h1 class="display-4 text-primary"><?php echo $progress['percentage']; ?>%</h1>
                                    <p class="text-muted">Hoàn thành</p>
                                </div>
                                <div class="progress mb-3">
                                    <div class="progress-bar" role="progressbar" 
                                         style="width: <?php echo $progress['percentage']; ?>%" 
                                         aria-valuenow="<?php echo $progress['percentage']; ?>" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                    </div>
                                </div>
                                <p class="text-center">
                                    <strong><?php echo $progress['completed']; ?></strong> / <strong><?php echo $progress['total']; ?></strong> giai đoạn hoàn thành
                                </p>
                                
                                <?php if ($progress['total'] > 0): ?>
                                <div class="mt-3">
                                    <div class="d-flex justify-content-between small text-muted">
                                        <span>0%</span>
                                        <span>100%</span>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Danh sách giai đoạn dưới dạng card -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span>
                                    <i class="bi bi-table"></i> Bảng giai đoạn trong kế hoạch
                                </span>
                                <a href="create_stage.php?plan_id=<?php echo $plan['id']; ?>" class="btn btn-primary btn-sm">
                                    <i class="bi bi-plus-lg"></i> Thêm giai đoạn
                                </a>
                            </div>
                            <div class="card-body">
                                <?php if (count($stages) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Tiêu đề</th>
                                                <th>Mô tả</th>
                                                <th>Ngày hết hạn</th>
                                                <th>Ưu tiên</th>
                                                <th>Trạng thái</th>
                                                <th>Hoạt động</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($stages as $index => $stage): 
                                                $isCompleted = ($stage['status'] === 'completed');
                                            ?>
                                            <tr class="<?php echo $isCompleted ? 'table-success' : ''; ?>">
                                                <td>
                                                    <strong><?php echo htmlspecialchars($stage['title']); ?></strong>
                                                </td>
                                                <td>
                                                    <?php if (!empty($stage['description'])): ?>
                                                        <?php echo htmlspecialchars(substr($stage['description'], 0, 100)); ?>
                                                        <?php echo strlen($stage['description']) > 100 ? '...' : ''; ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">Không có mô tả</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($stage['deadline'])): ?>
                                                        <?php echo date('d/m/Y', strtotime($stage['deadline'])); ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">Chưa xác định</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($stage['priority']) && $stage['priority'] !== 'medium'): ?>
                                                        <span class="badge <?php 
                                                            echo $stage['priority'] === 'high' ? 'bg-danger' : 'bg-success'; 
                                                        ?>">
                                                            <?php 
                                                            echo $stage['priority'] === 'high' ? 'Cao' : 'Thấp'; 
                                                            ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="text-muted">Trung bình</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge <?php echo $isCompleted ? 'bg-success' : 'bg-warning'; ?>">
                                                        <?php echo $isCompleted ? 'Đã hoàn thành' : 'Chưa hoàn thành'; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <!-- Checkbox đánh dấu hoàn thành -->
                                                    <form action="../../handle/study_plan_process.php" method="POST" class="d-inline">
                                                        <input type="hidden" name="action" value="update_stage_status">
                                                        <input type="hidden" name="stage_id" value="<?php echo $stage['id']; ?>">
                                                        <input type="hidden" name="plan_id" value="<?php echo $plan['id']; ?>">
                                                        <input type="hidden" name="status" value="<?php echo $isCompleted ? 'in_progress' : 'completed'; ?>">
                                                        
                                                        <div class="form-check form-switch d-inline-block me-2">
                                                            <input class="form-check-input" type="checkbox" id="completeSwitch<?php echo $stage['id']; ?>" <?php echo $isCompleted ? 'checked' : ''; ?> onchange="this.form.submit()">
                                                        </div>
                                                    </form>
                                                    
                                                    <!-- Các nút hành động -->
                                                    <div class="btn-group" role="group">
                                                        <a href="view_stage.php?id=<?php echo $stage['id']; ?>" 
                                                           class="btn btn-sm btn-outline-info" 
                                                           title="Xem chi tiết">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="edit_stage.php?id=<?php echo $stage['id']; ?>" 
                                                           class="btn btn-sm btn-outline-primary" 
                                                           title="Chỉnh sửa">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <a href="../../handle/study_plan_process.php?action=delete_stage&id=<?php echo $stage['id']; ?>&plan_id=<?php echo $plan['id']; ?>" 
                                                           class="btn btn-sm btn-outline-danger" 
                                                           title="Xóa" 
                                                           onclick="return confirm('Bạn có chắc chắn muốn xóa giai đoạn này?')">
                                                            <i class="bi bi-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="bi bi-list-task" style="font-size: 3rem; color: #ccc;"></i>
                                    <h4 class="mt-3">Chưa có giai đoạn nào</h4>
                                    <p class="text-muted">Kế hoạch này chưa có bất kỳ giai đoạn nào.</p>
                                    <a href="create_stage.php?plan_id=<?php echo $plan['id']; ?>" class="btn btn-primary">
                                        <i class="bi bi-plus-lg"></i> Thêm giai đoạn đầu tiên
                                    </a>
                                </div>
                                <?php endif; ?>
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