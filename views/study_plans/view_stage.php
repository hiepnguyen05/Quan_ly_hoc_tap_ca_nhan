<?php
require_once __DIR__ . '/../../functions/auth.php';
require_once __DIR__ . '/../../functions/stage_functions.php';
require_once __DIR__ . '/../../functions/study_plan_functions.php';
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
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết Giai đoạn - <?php echo htmlspecialchars($stage['title']); ?></title>
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
                                    <a href="view_plan.php?id=<?php echo $stage['plan_id']; ?>">
                                        <?php 
                                        // Lấy tên kế hoạch để hiển thị
                                        $plan = getStudyPlanById($stage['plan_id'], $currentUser['id']);
                                        echo htmlspecialchars($plan['title']);
                                        ?>
                                    </a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    <i class="bi bi-eye"></i> Chi tiết giai đoạn
                                </li>
                            </ol>
                        </nav>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <h2 class="page-title">
                            <i class="bi bi-journal-text"></i> Chi tiết Giai đoạn
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
                
                <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i>
                    <?php echo htmlspecialchars($_GET['success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <!-- Thông tin chi tiết giai đoạn -->
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <i class="bi bi-journal-text"></i> Thông tin giai đoạn
                            </div>
                            <div class="card-body">
                                <h3><?php echo htmlspecialchars($stage['title']); ?></h3>
                                
                                <?php if (!empty($stage['description'])): ?>
                                <div class="mb-3">
                                    <h5><i class="bi bi-card-text"></i> Mô tả:</h5>
                                    <p><?php echo nl2br(htmlspecialchars($stage['description'])); ?></p>
                                </div>
                                <?php endif; ?>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <h5><i class="bi bi-calendar-x"></i> Thời hạn:</h5>
                                            <p><?php echo date('d/m/Y', strtotime($stage['deadline'])); ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <h5><i class="bi bi-flag"></i> Mức độ ưu tiên:</h5>
                                            <p>
                                                <?php 
                                                switch ($stage['priority']) {
                                                    case 'low':
                                                        echo '<span class="badge bg-success">Thấp</span>';
                                                        break;
                                                    case 'medium':
                                                        echo '<span class="badge bg-warning">Trung bình</span>';
                                                        break;
                                                    case 'high':
                                                        echo '<span class="badge bg-danger">Cao</span>';
                                                        break;
                                                    default:
                                                        echo '<span class="badge bg-secondary">Không xác định</span>';
                                                }
                                                ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <h5><i class="bi bi-check-circle"></i> Trạng thái:</h5>
                                    <p>
                                        <?php 
                                        switch ($stage['status']) {
                                            case 'not_started':
                                                echo '<span class="badge bg-secondary">Chưa bắt đầu</span>';
                                                break;
                                            case 'in_progress':
                                                echo '<span class="badge bg-primary">Đang thực hiện</span>';
                                                break;
                                            case 'completed':
                                                echo '<span class="badge bg-success">Đã hoàn thành</span>';
                                                break;
                                            default:
                                                echo '<span class="badge bg-secondary">Không xác định</span>';
                                        }
                                        ?>
                                    </p>
                                </div>
                                
                                <div class="d-flex justify-content-between">
                                    <a href="view_plan.php?id=<?php echo $stage['plan_id']; ?>" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left"></i> Quay lại kế hoạch
                                    </a>
                                    <div>
                                        <a href="edit_stage.php?id=<?php echo $stage['id']; ?>" class="btn btn-primary">
                                            <i class="bi bi-pencil"></i> Chỉnh sửa
                                        </a>
                                        <a href="../../handle/study_plan_process.php?action=delete_stage&id=<?php echo $stage['id']; ?>" 
                                           class="btn btn-danger" 
                                           onclick="return confirm('Bạn có chắc chắn muốn xóa giai đoạn này?')">
                                            <i class="bi bi-trash"></i> Xóa
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <i class="bi bi-info-circle"></i> Thông tin bổ sung
                            </div>
                            <div class="card-body">
                                <h5><i class="bi bi-calendar-plus"></i> Ngày tạo:</h5>
                                <p><?php echo date('d/m/Y H:i', strtotime($stage['created_at'])); ?></p>
                                
                                <?php if (!empty($stage['updated_at']) && $stage['updated_at'] != $stage['created_at']): ?>
                                <h5><i class="bi bi-calendar-check"></i> Ngày cập nhật:</h5>
                                <p><?php echo date('d/m/Y H:i', strtotime($stage['updated_at'])); ?></p>
                                <?php endif; ?>
                                
                                <hr>
                                
                                <h5><i class="bi bi-lightbulb"></i> Hướng dẫn:</h5>
                                <ul>
                                    <li>Sử dụng nút "Chỉnh sửa" để cập nhật thông tin</li>
                                    <li>Cập nhật trạng thái khi bắt đầu hoặc hoàn thành</li>
                                    <li>Xóa giai đoạn nếu không còn cần thiết</li>
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