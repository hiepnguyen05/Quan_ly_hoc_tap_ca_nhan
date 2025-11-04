<?php
require_once __DIR__ . '/../functions/auth.php';
require_once __DIR__ . '/../functions/study_plan_functions.php';
require_once __DIR__ . '/../functions/stage_functions.php';
require_once __DIR__ . '/../functions/schedule_functions.php';
checkLogin(__DIR__ . '/../index.php');
$currentUser = getCurrentUser();

// Lấy thống kê kế hoạch học tập
$allPlans = getUserStudyPlans($currentUser['id']);
$totalPlans = count($allPlans);

// Đếm số kế hoạch theo trạng thái (chỉ hoàn thành và chưa hoàn thành)
$completedPlans = 0;
$incompletePlans = 0;
$inProgressPlans = 0;

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
}

// Lấy 5 kế hoạch gần đây nhất
$recentPlans = array_slice($allPlans, 0, 5);

// Lấy tất cả các giai đoạn từ tất cả kế hoạch
$allStages = [];
foreach ($allPlans as $plan) {
    $stages = getPlanStages($plan['id']);
    foreach ($stages as $stage) {
        $stage['plan_title'] = $plan['title'];
        $stage['plan_id'] = $plan['id'];
        $allStages[] = $stage;
    }
}

// Sắp xếp theo ngày tạo giảm dần và lấy 5 giai đoạn gần đây nhất
usort($allStages, function($a, $b) {
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});
$recentStages = array_slice($allStages, 0, 5);

// Tính toán tiến độ tổng thể
$totalStages = 0;
$completedStages = 0;
foreach ($allPlans as $plan) {
    $progress = calculatePlanProgress($plan['id']);
    $totalStages += $progress['total'];
    $completedStages += $progress['completed'];
}
$overallProgress = ($totalStages > 0) ? round(($completedStages / $totalStages) * 100) : 0;

// Tìm các kế hoạch sắp hết hạn nhưng chưa hoàn thành (trong vòng 7 ngày tới và tiến độ < 100%)
$urgentPlans = [];
$today = new DateTime();
$nextWeek = clone $today;
$nextWeek->add(new DateInterval('P7D'));

foreach ($allPlans as $plan) {
    // Chỉ xem xét các kế hoạch chưa hoàn thành
    $progress = calculatePlanProgress($plan['id']);
    if ($progress['percentage'] < 100 && !empty($plan['end_date'])) {
        $endDate = new DateTime($plan['end_date']);
        // Kiểm tra nếu ngày kết thúc trong vòng 7 ngày tới
        if ($endDate >= $today && $endDate <= $nextWeek) {
            $urgentPlans[] = $plan;
        }
    }
}

// Lấy thời khóa biểu cho ngày hôm nay
$todaySchedule = getActiveScheduleForToday($currentUser['id']);

// Xác định ngày hiện tại để làm nổi bật trong thời khóa biểu
$currentDayOfWeek = date('l'); // Lấy tên ngày tiếng Anh (Monday, Tuesday, v.v.)
$dayMapping = [
    'Monday' => 'monday',
    'Tuesday' => 'tuesday',
    'Wednesday' => 'wednesday',
    'Thursday' => 'thursday',
    'Friday' => 'friday',
    'Saturday' => 'saturday',
    'Sunday' => 'sunday'
];
$currentDay = $dayMapping[$currentDayOfWeek] ?? 'monday';
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Quản lý Kế hoạch Học tập Cá nhân</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="../css/dashboard.css" rel="stylesheet">
    <style>
        .today-highlight {
            background-color: #EFF6FF !important;
            border-left: 4px solid #3b82f6 !important;
            font-weight: bold;
        }
        .schedule-table th.today-header {
            background-color: #3b82f6 !important;
            color: #fff !important;
        }
        .urgent-plan-warning {
            background-color: #fffbeb !important;
            border: 1px solid #fef3c7 !important;
        }
        .urgent-plan-warning .card-header {
            background-color: #fef3c7 !important;
            border-bottom: 1px solid #fde68a !important;
        }
    </style>
</head>

<body>
    <?php 
    $currentPage = basename($_SERVER['PHP_SELF']);
    include 'components/header.php'; 
    ?>
    
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="d-md-block" style="padding-left: 0; padding-right: 0; width: 256px;">
            <?php include 'components/sidebar.php'; ?>
        </div>
        
        <!-- Main Content -->
        <div class="main-content" style="padding-top: 0; margin-top: 0; flex: 1;">
            <!-- Welcome Message -->
            <div class="welcome-message-container">
                <div class="col-12">
                    <div class="bg-white p-4 rounded shadow-sm">
                        <h2 class="mb-0">Xin chào! 👋</h2>
                        <p class="text-muted mb-0">
                            <?php 
                            $dayOfWeek = date('w');
                            $days = ['Chủ Nhật', 'Thứ Hai', 'Thứ Ba', 'Thứ Tư', 'Thứ Năm', 'Thứ Sáu', 'Thứ Bảy'];
                            echo 'Hôm nay là ' . $days[$dayOfWeek] . ', ngày ' . date('d \t\h\á\n\g n \nă\m Y');
                            ?>
                        </p>
                    </div>
                </div>
            </div>
                
            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-3 col-6 mb-4">
                    <div class="stats-card stats-card-blue">
                        <div class="icon-container">
                            <i class="bi bi-journal-bookmark"></i>
                        </div>
                        <h3><?php echo $totalPlans; ?></h3>
                        <p class="mb-0">Tổng kế hoạch</p>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-4">
                    <div class="stats-card stats-card-green">
                        <div class="icon-container">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <h3><?php echo $completedPlans; ?></h3>
                        <p class="mb-0">Đã hoàn thành</p>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-4">
                    <div class="stats-card stats-card-orange">
                        <div class="icon-container">
                            <i class="bi bi-play-circle"></i>
                        </div>
                        <h3><?php echo $inProgressPlans; ?></h3>
                        <p class="mb-0">Đang thực hiện</p>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-4">
                    <div class="stats-card stats-card-purple">
                        <div class="icon-container">
                            <i class="bi bi-list-check"></i>
                        </div>
                        <h3><?php echo $overallProgress; ?>%</h3>
                        <p class="mb-0">Tiến độ tổng thể</p>
                    </div>
                </div>
            </div>
            
            <!-- Main Content and Sidebar -->
            <div class="row">
                <!-- Main Content -->
                <div class="col-lg-8">
                    <!-- Recent Plans -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span>
                                <i class="bi bi-clock-history"></i> Kế hoạch học tập gần đây
                            </span>
                            <a href="study_plans/plan_list.php" class="btn btn-sm btn-primary">
                                <i class="bi bi-eye"></i> Xem tất cả
                            </a>
                        </div>
                        <div class="card-body">
                            <?php if (count($recentPlans) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Tiêu đề</th>
                                            <th>Ngày bắt đầu</th>
                                            <th>Ngày kết thúc</th>
                                            <th>Trạng thái</th>
                                            <th>Tiến độ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentPlans as $plan): 
                                            $progress = calculatePlanProgress($plan['id']);
                                            $isCompleted = ($progress['percentage'] == 100);
                                        ?>
                                        <tr>
                                            <td>
                                                <a href="study_plans/view_plan.php?id=<?php echo $plan['id']; ?>" class="text-decoration-none">
                                                    <?php echo htmlspecialchars($plan['title']); ?>
                                                </a>
                                            </td>
                                            <td>
                                                <?php echo !empty($plan['start_date']) ? date('d/m/Y', strtotime($plan['start_date'])) : '<span class="text-muted">Chưa xác định</span>'; ?>
                                            </td>
                                            <td>
                                                <?php echo !empty($plan['end_date']) ? date('d/m/Y', strtotime($plan['end_date'])) : '<span class="text-muted">Chưa xác định</span>'; ?>
                                            </td>
                                            <td>
                                                <?php
                                                if ($isCompleted) {
                                                    echo '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Đã hoàn thành</span>';
                                                } else {
                                                    echo '<span class="badge bg-warning"><i class="bi bi-clock"></i> Chưa hoàn thành</span>';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <div class="progress-container">
                                                    <div class="progress">
                                                        <div class="progress-bar" role="progressbar" 
                                                             style="width: <?php echo $progress['percentage']; ?>%" 
                                                             aria-valuenow="<?php echo $progress['percentage']; ?>" 
                                                             aria-valuemin="0" 
                                                             aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                    <div class="percentage">
                                                        <?php echo $progress['percentage']; ?>%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <div class="text-center py-5">
                                <i class="bi bi-journal-text" style="font-size: 3rem; color: #ccc;"></i>
                                <p class="mt-3">Bạn chưa có kế hoạch học tập nào.</p>
                                <a href="study_plans/create_plan.php" class="btn btn-primary">
                                    <i class="bi bi-plus-lg"></i> Tạo kế hoạch đầu tiên
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Today's Schedule -->
                    <?php if ($todaySchedule): ?>
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span>
                                <i class="bi bi-calendar-event"></i> Thời khóa biểu hôm nay
                            </span>
                            <a href="schedule/view_schedule.php?id=<?php echo $todaySchedule['id']; ?>" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> Xem chi tiết
                            </a>
                        </div>
                        <div class="card-body">
                            <h5><?php echo htmlspecialchars($todaySchedule['schedule_name']); ?></h5>
                            
                            <!-- Schedule Table -->
                            <div class="table-responsive">
                                <table class="table table-bordered schedule-table">
                                    <thead>
                                        <tr>
                                            <th>Thời gian</th>
                                            <th class="<?php echo $currentDay === 'monday' ? 'today-header' : ''; ?>">Thứ Hai</th>
                                            <th class="<?php echo $currentDay === 'tuesday' ? 'today-header' : ''; ?>">Thứ Ba</th>
                                            <th class="<?php echo $currentDay === 'wednesday' ? 'today-header' : ''; ?>">Thứ Tư</th>
                                            <th class="<?php echo $currentDay === 'thursday' ? 'today-header' : ''; ?>">Thứ Năm</th>
                                            <th class="<?php echo $currentDay === 'friday' ? 'today-header' : ''; ?>">Thứ Sáu</th>
                                            <th class="<?php echo $currentDay === 'saturday' ? 'today-header' : ''; ?>">Thứ Bảy</th>
                                            <th class="<?php echo $currentDay === 'sunday' ? 'today-header' : ''; ?>">Chủ Nhật</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Morning -->
                                        <tr>
                                            <td>Sáng (7h-11h)</td>
                                            <td class="<?php echo $currentDay === 'monday' ? 'today-highlight' : ''; ?>">
                                                <?php 
                                                foreach ($todaySchedule['items'] as $item) {
                                                    if ($item['day_of_week'] === 'monday' && $item['time_slot'] === 'morning') {
                                                        echo htmlspecialchars($item['plan_title']);
                                                        break;
                                                    }
                                                }
                                                ?>
                                            </td>
                                            <td class="<?php echo $currentDay === 'tuesday' ? 'today-highlight' : ''; ?>">
                                                <?php 
                                                foreach ($todaySchedule['items'] as $item) {
                                                    if ($item['day_of_week'] === 'tuesday' && $item['time_slot'] === 'morning') {
                                                        echo htmlspecialchars($item['plan_title']);
                                                        break;
                                                    }
                                                }
                                                ?>
                                            </td>
                                            <td class="<?php echo $currentDay === 'wednesday' ? 'today-highlight' : ''; ?>">
                                                <?php 
                                                foreach ($todaySchedule['items'] as $item) {
                                                    if ($item['day_of_week'] === 'wednesday' && $item['time_slot'] === 'morning') {
                                                        echo htmlspecialchars($item['plan_title']);
                                                        break;
                                                    }
                                                }
                                                ?>
                                            </td>
                                            <td class="<?php echo $currentDay === 'thursday' ? 'today-highlight' : ''; ?>">
                                                <?php 
                                                foreach ($todaySchedule['items'] as $item) {
                                                    if ($item['day_of_week'] === 'thursday' && $item['time_slot'] === 'morning') {
                                                        echo htmlspecialchars($item['plan_title']);
                                                        break;
                                                    }
                                                }
                                                ?>
                                            </td>
                                            <td class="<?php echo $currentDay === 'friday' ? 'today-highlight' : ''; ?>">
                                                <?php 
                                                foreach ($todaySchedule['items'] as $item) {
                                                    if ($item['day_of_week'] === 'friday' && $item['time_slot'] === 'morning') {
                                                        echo htmlspecialchars($item['plan_title']);
                                                        break;
                                                    }
                                                }
                                                ?>
                                            </td>
                                            <td class="<?php echo $currentDay === 'saturday' ? 'today-highlight' : ''; ?>">
                                                <?php 
                                                foreach ($todaySchedule['items'] as $item) {
                                                    if ($item['day_of_week'] === 'saturday' && $item['time_slot'] === 'morning') {
                                                        echo htmlspecialchars($item['plan_title']);
                                                        break;
                                                    }
                                                }
                                                ?>
                                            </td>
                                            <td class="<?php echo $currentDay === 'sunday' ? 'today-highlight' : ''; ?>">
                                                <?php 
                                                foreach ($todaySchedule['items'] as $item) {
                                                    if ($item['day_of_week'] === 'sunday' && $item['time_slot'] === 'morning') {
                                                        echo htmlspecialchars($item['plan_title']);
                                                        break;
                                                    }
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <!-- Afternoon -->
                                        <tr>
                                            <td>Chiều (13h-17h)</td>
                                            <td class="<?php echo $currentDay === 'monday' ? 'today-highlight' : ''; ?>">
                                                <?php 
                                                foreach ($todaySchedule['items'] as $item) {
                                                    if ($item['day_of_week'] === 'monday' && $item['time_slot'] === 'afternoon') {
                                                        echo htmlspecialchars($item['plan_title']);
                                                        break;
                                                    }
                                                }
                                                ?>
                                            </td>
                                            <td class="<?php echo $currentDay === 'tuesday' ? 'today-highlight' : ''; ?>">
                                                <?php 
                                                foreach ($todaySchedule['items'] as $item) {
                                                    if ($item['day_of_week'] === 'tuesday' && $item['time_slot'] === 'afternoon') {
                                                        echo htmlspecialchars($item['plan_title']);
                                                        break;
                                                    }
                                                }
                                                ?>
                                            </td>
                                            <td class="<?php echo $currentDay === 'wednesday' ? 'today-highlight' : ''; ?>">
                                                <?php 
                                                foreach ($todaySchedule['items'] as $item) {
                                                    if ($item['day_of_week'] === 'wednesday' && $item['time_slot'] === 'afternoon') {
                                                        echo htmlspecialchars($item['plan_title']);
                                                        break;
                                                    }
                                                }
                                                ?>
                                            </td>
                                            <td class="<?php echo $currentDay === 'thursday' ? 'today-highlight' : ''; ?>">
                                                <?php 
                                                foreach ($todaySchedule['items'] as $item) {
                                                    if ($item['day_of_week'] === 'thursday' && $item['time_slot'] === 'afternoon') {
                                                        echo htmlspecialchars($item['plan_title']);
                                                        break;
                                                    }
                                                }
                                                ?>
                                            </td>
                                            <td class="<?php echo $currentDay === 'friday' ? 'today-highlight' : ''; ?>">
                                                <?php 
                                                foreach ($todaySchedule['items'] as $item) {
                                                    if ($item['day_of_week'] === 'friday' && $item['time_slot'] === 'afternoon') {
                                                        echo htmlspecialchars($item['plan_title']);
                                                        break;
                                                    }
                                                }
                                                ?>
                                            </td>
                                            <td class="<?php echo $currentDay === 'saturday' ? 'today-highlight' : ''; ?>">
                                                <?php 
                                                foreach ($todaySchedule['items'] as $item) {
                                                    if ($item['day_of_week'] === 'saturday' && $item['time_slot'] === 'afternoon') {
                                                        echo htmlspecialchars($item['plan_title']);
                                                        break;
                                                    }
                                                }
                                                ?>
                                            </td>
                                            <td class="<?php echo $currentDay === 'sunday' ? 'today-highlight' : ''; ?>">
                                                <?php 
                                                foreach ($todaySchedule['items'] as $item) {
                                                    if ($item['day_of_week'] === 'sunday' && $item['time_slot'] === 'afternoon') {
                                                        echo htmlspecialchars($item['plan_title']);
                                                        break;
                                                    }
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <!-- Evening -->
                                        <tr>
                                            <td>Tối (19h-21h)</td>
                                            <td class="<?php echo $currentDay === 'monday' ? 'today-highlight' : ''; ?>">
                                                <?php 
                                                foreach ($todaySchedule['items'] as $item) {
                                                    if ($item['day_of_week'] === 'monday' && $item['time_slot'] === 'evening') {
                                                        echo htmlspecialchars($item['plan_title']);
                                                        break;
                                                    }
                                                }
                                                ?>
                                            </td>
                                            <td class="<?php echo $currentDay === 'tuesday' ? 'today-highlight' : ''; ?>">
                                                <?php 
                                                foreach ($todaySchedule['items'] as $item) {
                                                    if ($item['day_of_week'] === 'tuesday' && $item['time_slot'] === 'evening') {
                                                        echo htmlspecialchars($item['plan_title']);
                                                        break;
                                                    }
                                                }
                                                ?>
                                            </td>
                                            <td class="<?php echo $currentDay === 'wednesday' ? 'today-highlight' : ''; ?>">
                                                <?php 
                                                foreach ($todaySchedule['items'] as $item) {
                                                    if ($item['day_of_week'] === 'wednesday' && $item['time_slot'] === 'evening') {
                                                        echo htmlspecialchars($item['plan_title']);
                                                        break;
                                                    }
                                                }
                                                ?>
                                            </td>
                                            <td class="<?php echo $currentDay === 'thursday' ? 'today-highlight' : ''; ?>">
                                                <?php 
                                                foreach ($todaySchedule['items'] as $item) {
                                                    if ($item['day_of_week'] === 'thursday' && $item['time_slot'] === 'evening') {
                                                        echo htmlspecialchars($item['plan_title']);
                                                        break;
                                                    }
                                                }
                                                ?>
                                            </td>
                                            <td class="<?php echo $currentDay === 'friday' ? 'today-highlight' : ''; ?>">
                                                <?php 
                                                foreach ($todaySchedule['items'] as $item) {
                                                    if ($item['day_of_week'] === 'friday' && $item['time_slot'] === 'evening') {
                                                        echo htmlspecialchars($item['plan_title']);
                                                        break;
                                                    }
                                                }
                                                ?>
                                            </td>
                                            <td class="<?php echo $currentDay === 'saturday' ? 'today-highlight' : ''; ?>">
                                                <?php 
                                                foreach ($todaySchedule['items'] as $item) {
                                                    if ($item['day_of_week'] === 'saturday' && $item['time_slot'] === 'evening') {
                                                        echo htmlspecialchars($item['plan_title']);
                                                        break;
                                                    }
                                                }
                                                ?>
                                            </td>
                                            <td class="<?php echo $currentDay === 'sunday' ? 'today-highlight' : ''; ?>">
                                                <?php 
                                                foreach ($todaySchedule['items'] as $item) {
                                                    if ($item['day_of_week'] === 'sunday' && $item['time_slot'] === 'evening') {
                                                        echo htmlspecialchars($item['plan_title']);
                                                        break;
                                                    }
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Time Study Chart -->
                    <div class="card">
                        <div class="card-header">
                            <i class="bi bi-bar-chart"></i> Thời Gian Học Trong Tuần
                        </div>
                        <div class="card-body">
                            <div class="text-center py-5">
                                <i class="bi bi-graph-up" style="font-size: 3rem; color: #ccc;"></i>
                                <h4 class="mt-3">Biểu đồ thời gian học</h4>
                                <p class="text-muted">Chức năng này sẽ được cập nhật trong phiên bản tiếp theo</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Sidebar Content -->
                <div class="col-lg-4">
                    <!-- Urgent Plans Warning -->
                    <div class="card mb-4 urgent-plan-warning">
                        <div class="card-header bg-warning text-dark">
                            <i class="bi bi-exclamation-triangle"></i> Kế hoạch sắp hết hạn
                        </div>
                        <div class="card-body">
                            <?php if (count($urgentPlans) > 0): ?>
                            <p class="mb-3">Các kế hoạch sau đây sắp đến hạn trong vòng 7 ngày nhưng vẫn chưa hoàn thành:</p>
                            <div class="list-group">
                                <?php foreach ($urgentPlans as $plan): 
                                    $progress = calculatePlanProgress($plan['id']);
                                    $endDate = new DateTime($plan['end_date']);
                                    $today = new DateTime();
                                    $interval = $today->diff($endDate);
                                    $daysLeft = $interval->days;
                                ?>
                                <a href="study_plans/view_plan.php?id=<?php echo $plan['id']; ?>" class="list-group-item list-group-item-action">
                                    <div class="d-flex justify-content-between">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($plan['title']); ?></h6>
                                        <span class="badge bg-warning"><?php echo $daysLeft; ?> ngày</span>
                                    </div>
                                    <div class="progress mt-2">
                                        <div class="progress-bar" role="progressbar" 
                                             style="width: <?php echo $progress['percentage']; ?>%" 
                                             aria-valuenow="<?php echo $progress['percentage']; ?>" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                    <small class="text-muted">
                                        Kết thúc: <?php echo date('d/m/Y', strtotime($plan['end_date'])); ?>
                                    </small>
                                </a>
                                <?php endforeach; ?>
                            </div>
                            <?php else: ?>
                            <div class="text-center py-3">
                                <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
                                <p class="mt-2 mb-0">Không có kế hoạch nào sắp hết hạn!</p>
                                <p class="text-muted small">Tất cả các kế hoạch đều đang trong tiến độ tốt.</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Calendar -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="bi bi-calendar"></i> Lịch Học
                        </div>
                        <div class="card-body">
                            <?php if ($todaySchedule): ?>
                            <div class="text-center py-3">
                                <i class="bi bi-calendar-event" style="font-size: 2rem; color: #0d6efd;"></i>
                                <p class="mt-2 mb-0">Lịch học hôm nay</p>
                                <p class="text-muted small"><?php echo htmlspecialchars($todaySchedule['schedule_name']); ?></p>
                                
                                <ul class="list-unstyled mt-3 text-start">
                                    <?php 
                                    $hasScheduleItems = false;
                                    foreach ($todaySchedule['items'] as $item) {
                                        if ($item['day_of_week'] === $currentDay) {
                                            $hasScheduleItems = true;
                                            $timeSlotText = '';
                                            switch ($item['time_slot']) {
                                                case 'morning': $timeSlotText = 'Sáng'; break;
                                                case 'afternoon': $timeSlotText = 'Chiều'; break;
                                                case 'evening': $timeSlotText = 'Tối'; break;
                                            }
                                            echo '<li class="mb-2"><i class="bi bi-circle-fill text-primary me-2" style="font-size: 0.5rem;"></i>' . $timeSlotText . ': ' . htmlspecialchars($item['plan_title']) . '</li>';
                                        }
                                    }
                                    
                                    if (!$hasScheduleItems) {
                                        echo '<li class="mb-2"><i class="bi bi-circle-fill text-muted me-2" style="font-size: 0.5rem;"></i> Không có lịch học</li>';
                                    }
                                    ?>
                                </ul>
                                
                                <a href="schedule/view_schedule.php?id=<?php echo $todaySchedule['id']; ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> Xem thời khóa biểu đầy đủ
                                </a>
                            </div>
                            <?php else: ?>
                            <div class="text-center py-3">
                                <i class="bi bi-calendar-x" style="font-size: 2rem; color: #ccc;"></i>
                                <p class="mt-2 mb-0">Không có thời khóa biểu</p>
                                <p class="text-muted small">Chưa có thời khóa biểu nào được tạo cho hôm nay</p>
                                <a href="schedule/create_schedule.php" class="btn btn-sm btn-primary">
                                    <i class="bi bi-plus-lg"></i> Tạo thời khóa biểu
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Recent Notes -->
                    <div class="card">
                        <div class="card-header">
                            <i class="bi bi-journal-text"></i> Ghi Chú Gần Đây
                        </div>
                        <div class="card-body">
                            <div class="text-center py-3">
                                <i class="bi bi-stickies" style="font-size: 2rem; color: #ccc;"></i>
                                <p class="mt-2 mb-0">Chưa có ghi chú nào</p>
                                <p class="text-muted small">Chức năng này sẽ được cập nhật trong phiên bản tiếp theo</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle sidebar on mobile
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.querySelector('.menu-toggle');
            const sidebar = document.querySelector('.sidebar');
            
            if (menuToggle) {
                menuToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                });
            }
        });
    </script>
</body>

</html>