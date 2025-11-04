<?php
// Lấy tất cả người dùng và kế hoạch học tập
require_once __DIR__ . '/../../functions/study_plan_functions.php';
$allUsers = getAllUsers();
$reportData = [];

$totalPlans = 0;
$totalStages = 0;
$completedStages = 0;

foreach ($allUsers as $user) {
    $plans = getUserStudyPlans($user['id']);
    $userPlanCount = count($plans);
    $userStageCount = 0;
    $userCompletedStageCount = 0;
    
    foreach ($plans as $plan) {
        $progress = calculatePlanProgress($plan['id']);
        $userStageCount += $progress['total'];
        $userCompletedStageCount += $progress['completed'];
        
        $totalStages += $progress['total'];
        $completedStages += $progress['completed'];
    }
    
    $reportData[] = [
        'user' => $user,
        'plan_count' => $userPlanCount,
        'stage_count' => $userStageCount,
        'completed_stage_count' => $userCompletedStageCount,
        'completion_rate' => $userStageCount > 0 ? round(($userCompletedStageCount / $userStageCount) * 100, 2) : 0
    ];
    
    $totalPlans += $userPlanCount;
}

$overallCompletionRate = $totalStages > 0 ? round(($completedStages / $totalStages) * 100, 2) : 0;
?>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3 col-6 mb-4">
        <div class="stats-card stats-card-blue">
            <div class="icon-container">
                <i class="bi bi-people"></i>
            </div>
            <h2><?php echo count($allUsers); ?></h2>
            <h5>Người dùng</h5>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-4">
        <div class="stats-card stats-card-green">
            <div class="icon-container">
                <i class="bi bi-journal-bookmark"></i>
            </div>
            <h2><?php echo $totalPlans; ?></h2>
            <h5>Kế hoạch</h5>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-4">
        <div class="stats-card stats-card-info">
            <div class="icon-container">
                <i class="bi bi-list-check"></i>
            </div>
            <h2><?php echo $totalStages; ?></h2>
            <h5>Giai đoạn</h5>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-4">
        <div class="stats-card stats-card-warning">
            <div class="icon-container">
                <i class="bi bi-bar-chart"></i>
            </div>
            <h2><?php echo $overallCompletionRate; ?>%</h2>
            <h5>Hoàn thành</h5>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="row">
    <div class="col-12">
        <!-- User Progress Report -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>
                    <i class="bi bi-file-bar-graph"></i> Báo cáo tiến độ người dùng
                </span>
                <div>
                    <button class="btn btn-sm btn-outline-secondary" onclick="exportReport()">
                        <i class="bi bi-download"></i> Xuất báo cáo
                    </button>
                </div>
            </div>
            <div class="card-body">
                <?php if (count($reportData) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Người dùng</th>
                                <th>Vai trò</th>
                                <th>Số kế hoạch</th>
                                <th>Tổng giai đoạn</th>
                                <th>Đã hoàn thành</th>
                                <th>Tỷ lệ hoàn thành</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reportData as $data): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($data['user']['full_name']); ?></strong>
                                    <div class="small text-muted"><?php echo htmlspecialchars($data['user']['username']); ?></div>
                                </td>
                                <td>
                                    <?php if ($data['user']['role'] === 'admin'): ?>
                                        <span class="badge bg-danger">Quản trị viên</span>
                                    <?php else: ?>
                                        <span class="badge bg-primary">Người dùng</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $data['plan_count']; ?></td>
                                <td><?php echo $data['stage_count']; ?></td>
                                <td><?php echo $data['completed_stage_count']; ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1 me-2" style="height: 10px;">
                                            <div class="progress-bar" role="progressbar" 
                                                 style="width: <?php echo $data['completion_rate']; ?>%" 
                                                 aria-valuenow="<?php echo $data['completion_rate']; ?>" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                            </div>
                                        </div>
                                        <span><?php echo $data['completion_rate']; ?>%</span>
                                    </div>
                                </td>
                                <td>
                                    <a href="../study_plans/plan_list.php?user_id=<?php echo $data['user']['id']; ?>" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="Xem kế hoạch">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-file-bar-graph" style="font-size: 3rem; color: #ccc;"></i>
                    <p class="mt-3">Chưa có dữ liệu báo cáo.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    function exportReport() {
        alert('Chức năng xuất báo cáo sẽ được triển khai trong phiên bản tiếp theo.');
    }
</script>