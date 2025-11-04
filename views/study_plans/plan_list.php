<?php
require_once __DIR__ . '/../../functions/auth.php';
require_once __DIR__ . '/../../functions/study_plan_functions.php';
checkLogin(__DIR__ . '/../../index.php');
$currentUser = getCurrentUser();

// Xác định loại kế hoạch cần hiển thị dựa trên URL
$filter = $_GET['filter'] ?? 'all';

// Lấy danh sách kế hoạch của người dùng
$allPlans = getUserStudyPlans($currentUser['id']);

// Lọc kế hoạch theo trạng thái
switch ($filter) {
    case 'completed':
        $plans = array_filter($allPlans, function($plan) {
            $progress = calculatePlanProgress($plan['id']);
            return $progress['percentage'] == 100;
        });
        $pageTitle = "Kế hoạch đã hoàn thành";
        break;
    case 'incomplete':
        $plans = array_filter($allPlans, function($plan) {
            $progress = calculatePlanProgress($plan['id']);
            return $progress['percentage'] < 100;
        });
        $pageTitle = "Kế hoạch chưa hoàn thành";
        break;
    case 'in_progress':
        $plans = array_filter($allPlans, function($plan) {
            $progress = calculatePlanProgress($plan['id']);
            return $progress['percentage'] > 0 && $progress['percentage'] < 100;
        });
        $pageTitle = "Kế hoạch đang thực hiện";
        break;
    default:
        $plans = $allPlans;
        $pageTitle = "Tất cả kế hoạch";
        break;
}

// Chuyển đổi mảng để đảm bảo chỉ số liên tục
$plans = array_values($plans);

// Tính toán thống kê
$totalPlans = count($allPlans);
$completedPlans = 0;
$incompletePlans = 0;
$inProgressPlans = 0;
$totalStages = 0;
$completedStages = 0;

foreach ($allPlans as $plan) {
    $progress = calculatePlanProgress($plan['id']);
    if ($progress['percentage'] == 100) {
        $completedPlans++;
    } else {
        $incompletePlans++;
        if ($progress['percentage'] > 0) {
            $inProgressPlans++;
        }
    }
    
    $totalStages += $progress['total'];
    $completedStages += $progress['completed'];
}

$overallProgress = ($totalStages > 0) ? round(($completedStages / $totalStages) * 100) : 0;
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Quản lý Kế hoạch Học tập Cá nhân</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="../../css/dashboard.css" rel="stylesheet">
    <style>
        .plan-card {
            transition: transform 0.2s;
            cursor: pointer;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .plan-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .plan-card .card-body {
            padding: 1.25rem;
        }
        .progress-container {
            display: flex;
            align-items: center;
        }
        .progress-container .progress {
            flex-grow: 1;
            height: 10px;
            margin-right: 10px;
        }
        .progress-container .percentage {
            font-weight: bold;
            min-width: 40px;
        }
        .plan-status-badge {
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
                                    <a href="../../dashboard.php">
                                        <i class="bi bi-speedometer2"></i> Dashboard
                                    </a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    <i class="bi bi-journal-bookmark"></i> <?php echo $pageTitle; ?>
                                </li>
                            </ol>
                        </nav>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <h2 class="page-title">
                            <i class="bi bi-list-ul"></i> <?php echo $pageTitle; ?>
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
                
                <!-- Button tạo kế hoạch mới -->
                <div class="row mb-4">
                    <div class="col-12">
                        <a href="create_plan.php" class="btn btn-primary">
                            <i class="bi bi-plus-lg"></i> Tạo kế hoạch mới
                        </a>
                    </div>
                </div>
                
                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="stats-card stats-card-blue">
                            <div class="icon-container">
                                <i class="bi bi-journal-bookmark"></i>
                            </div>
                            <h3><?php echo $totalPlans; ?></h3>
                            <p class="mb-0">Tổng kế hoạch</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="stats-card stats-card-green">
                            <div class="icon-container">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <h3><?php echo $completedPlans; ?></h3>
                            <p class="mb-0">Kế hoạch hoàn thành</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="stats-card stats-card-orange">
                            <div class="icon-container">
                                <i class="bi bi-play-circle"></i>
                            </div>
                            <h3><?php echo $inProgressPlans; ?></h3>
                            <p class="mb-0">Đang thực hiện</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="stats-card stats-card-purple">
                            <div class="icon-container">
                                <i class="bi bi-list-task"></i>
                            </div>
                            <h3><?php echo $overallProgress; ?>%</h3>
                            <p class="mb-0">Tiến độ tổng thể</p>
                        </div>
                    </div>
                </div>
                
                <!-- Danh sách kế hoạch dưới dạng card -->
                <div class="row">
                    <div class="col-12">
                        <h4><i class="bi bi-card-list"></i> <?php echo $pageTitle; ?></h4>
                    </div>
                </div>
                
                <?php if (count($plans) > 0): ?>
                <div class="row">
                    <?php foreach ($plans as $index => $plan): 
                        $progress = calculatePlanProgress($plan['id']);
                        $isCompleted = ($progress['percentage'] == 100);
                    ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card plan-card h-100" onclick="window.location='view_plan.php?id=<?php echo $plan['id']; ?>'">
                            <div class="card-body d-flex flex-column">
                                <div class="position-relative flex-grow-1">
                                    <h5 class="card-title"><?php echo htmlspecialchars($plan['title']); ?></h5>
                                    
                                    <?php if (!empty($plan['description'])): ?>
                                    <p class="card-text small text-muted">
                                        <?php echo htmlspecialchars(substr($plan['description'], 0, 80)); ?>
                                        <?php echo strlen($plan['description']) > 80 ? '...' : ''; ?>
                                    </p>
                                    <?php endif; ?>
                                    
                                    <div class="mb-2">
                                        <?php if (!empty($plan['start_date']) || !empty($plan['end_date'])): ?>
                                        <div class="small">
                                            <?php if (!empty($plan['start_date'])): ?>
                                            <div>
                                                <i class="bi bi-calendar-check"></i> 
                                                <?php echo date('d/m/Y', strtotime($plan['start_date'])); ?>
                                            </div>
                                            <?php endif; ?>
                                            <?php if (!empty($plan['end_date'])): ?>
                                            <div>
                                                <i class="bi bi-calendar-x"></i> 
                                                <?php echo date('d/m/Y', strtotime($plan['end_date'])); ?>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                        <?php else: ?>
                                        <span class="text-muted small">Thời gian chưa xác định</span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Badge trạng thái -->
                                    <span class="plan-status-badge badge <?php echo $isCompleted ? 'bg-success' : 'bg-warning'; ?>">
                                        <?php echo $isCompleted ? 'Hoàn thành' : 'Chưa hoàn thành'; ?>
                                    </span>
                                </div>
                                
                                <!-- Progress bar -->
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between small mb-1">
                                        <span>Tiến độ</span>
                                        <span><?php echo $progress['percentage']; ?>%</span>
                                    </div>
                                    <div class="progress-container">
                                        <div class="progress">
                                            <div class="progress-bar" role="progressbar" 
                                                 style="width: <?php echo $progress['percentage']; ?>%" 
                                                 aria-valuenow="<?php echo $progress['percentage']; ?>" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="small text-muted mt-1">
                                        <?php echo $progress['completed']; ?>/<?php echo $progress['total']; ?> giai đoạn
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Card footer với buttons -->
                            <div class="card-footer bg-white">
                                <div class="d-flex justify-content-between">
                                    <a href="view_plan.php?id=<?php echo $plan['id']; ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Xem
                                    </a>
                                    <a href="edit_plan.php?id=<?php echo $plan['id']; ?>" class="btn btn-sm btn-outline-warning">
                                        <i class="bi bi-pencil"></i> Sửa
                                    </a>
                                    <a href="../../handle/study_plan_process.php?action=delete&id=<?php echo $plan['id']; ?>" 
                                       class="btn btn-sm btn-outline-danger" 
                                       onclick="event.stopPropagation(); return confirm('Bạn có chắc chắn muốn xóa kế hoạch này?')">
                                        <i class="bi bi-trash"></i> Xóa
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="row">
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="bi bi-journal-text" style="font-size: 3rem; color: #ccc;"></i>
                            <h4 class="mt-3">Không có kế hoạch nào</h4>
                            <p class="text-muted">Không tìm thấy kế hoạch nào trong danh sách này.</p>
                            <a href="create_plan.php" class="btn btn-primary">
                                <i class="bi bi-plus-lg"></i> Tạo kế hoạch đầu tiên
                            </a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Quản lý giai đoạn -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <i class="bi bi-list-check"></i> Quản lý giai đoạn
                            </div>
                            <div class="card-body">
                                <p>Trong mỗi kế hoạch học tập, bạn có thể thêm và quản lý các giai đoạn cụ thể. 
                                Nhấn vào nút "Xem" trên mỗi card kế hoạch để xem chi tiết và thêm giai đoạn cho kế hoạch đó.</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <a href="../../dashboard.php" class="btn btn-outline-primary">
                                            <i class="bi bi-speedometer2"></i> Về Dashboard
                                        </a>
                                    </div>
                                    <div>
                                        <a href="create_plan.php" class="btn btn-primary">
                                            <i class="bi bi-plus-lg"></i> Tạo kế hoạch mới
                                        </a>
                                    </div>
                                </div>
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