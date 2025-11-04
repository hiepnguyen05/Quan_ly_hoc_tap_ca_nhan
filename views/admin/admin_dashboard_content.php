<?php
// Lấy thống kê
require_once __DIR__ . '/../../functions/study_plan_functions.php';
$allUsers = getAllUsers();
$totalUsers = count($allUsers);
$totalPlans = 0;
$completedPlans = 0;
$incompletePlans = 0;

foreach ($allUsers as $user) {
    $plans = getUserStudyPlans($user['id']);
    $totalPlans += count($plans);
    
    foreach ($plans as $plan) {
        $progress = calculatePlanProgress($plan['id']);
        if ($progress['percentage'] == 100) {
            $completedPlans++;
        } else {
            $incompletePlans++;
        }
    }
}

// Lấy 5 người dùng mới nhất
$recentUsers = array_slice($allUsers, 0, 5);

// Lấy 5 kế hoạch mới nhất từ tất cả người dùng
$allPlans = [];
foreach ($allUsers as $user) {
    $userPlans = getUserStudyPlans($user['id']);
    foreach ($userPlans as $plan) {
        $plan['user_name'] = $user['full_name'];
        $allPlans[] = $plan;
    }
}

// Sắp xếp theo ngày tạo giảm dần và lấy 5 kế hoạch gần đây nhất
usort($allPlans, function($a, $b) {
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});
$recentPlans = array_slice($allPlans, 0, 5);
?>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-4 col-6 mb-4">
        <div class="stats-card stats-card-blue">
            <div class="icon-container">
                <i class="bi bi-people"></i>
            </div>
            <h2><?php echo $totalUsers; ?></h2>
            <h5>Người dùng</h5>
        </div>
    </div>
    <div class="col-md-4 col-6 mb-4">
        <div class="stats-card stats-card-green">
            <div class="icon-container">
                <i class="bi bi-journal-bookmark"></i>
            </div>
            <h2><?php echo $totalPlans; ?></h2>
            <h5>Kế hoạch</h5>
        </div>
    </div>
    <div class="col-md-4 col-6 mb-4">
        <div class="stats-card stats-card-orange">
            <div class="icon-container">
                <i class="bi bi-clock"></i>
            </div>
            <h2><?php echo $incompletePlans; ?></h2>
            <h5>Chưa hoàn thành</h5>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="row">
    <div class="col-lg-8">
        <!-- Recent Users -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>
                    <i class="bi bi-people"></i> Người dùng mới đăng ký
                </span>
                <a href="user_management.php" class="btn btn-sm btn-primary">
                    <i class="bi bi-eye"></i> Xem tất cả
                </a>
            </div>
            <div class="card-body">
                <?php if (count($recentUsers) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Họ tên</th>
                                <th>Tên đăng nhập</th>
                                <th>Email</th>
                                <th>Vai trò</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentUsers as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <?php if ($user['role'] === 'admin'): ?>
                                        <span class="badge bg-danger">Quản trị viên</span>
                                    <?php else: ?>
                                        <span class="badge bg-primary">Người dùng</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-people" style="font-size: 3rem; color: #ccc;"></i>
                    <p class="mt-3">Chưa có người dùng nào.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Recent Plans -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>
                    <i class="bi bi-journal-bookmark"></i> Kế hoạch học tập mới
                </span>
                <a href="../study_plans/plan_list.php" class="btn btn-sm btn-primary">
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
                                <th>Người tạo</th>
                                <th>Ngày tạo</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentPlans as $plan): 
                                $progress = calculatePlanProgress($plan['id']);
                                $isCompleted = ($progress['percentage'] == 100);
                            ?>
                            <tr>
                                <td>
                                    <a href="../study_plans/view_plan.php?id=<?php echo $plan['id']; ?>" class="text-decoration-none">
                                        <?php echo htmlspecialchars($plan['title']); ?>
                                    </a>
                                </td>
                                <td><?php echo htmlspecialchars($plan['user_name']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($plan['created_at'])); ?></td>
                                <td>
                                    <?php
                                    if ($isCompleted) {
                                        echo '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Đã hoàn thành</span>';
                                    } else {
                                        echo '<span class="badge bg-warning"><i class="bi bi-clock"></i> Chưa hoàn thành</span>';
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-journal-text" style="font-size: 3rem; color: #ccc;"></i>
                    <p class="mt-3">Chưa có kế hoạch học tập nào.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Sidebar Content -->
    <div class="col-lg-4">
        <!-- System Information -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> Thông tin hệ thống
            </div>
            <div class="card-body">
                <div class="system-info-item">
                    <div class="d-flex justify-content-between">
                        <span><i class="bi bi-people me-2 text-primary"></i> Tổng người dùng:</span>
                        <strong><?php echo $totalUsers; ?></strong>
                    </div>
                </div>
                <div class="system-info-item">
                    <div class="d-flex justify-content-between">
                        <span><i class="bi bi-journal-bookmark me-2 text-success"></i> Tổng kế hoạch:</span>
                        <strong><?php echo $totalPlans; ?></strong>
                    </div>
                </div>
                <div class="system-info-item">
                    <div class="d-flex justify-content-between">
                        <span><i class="bi bi-check-circle me-2 text-info"></i> Kế hoạch hoàn thành:</span>
                        <strong><?php echo $completedPlans; ?></strong>
                    </div>
                </div>
                <div class="system-info-item">
                    <div class="d-flex justify-content-between">
                        <span><i class="bi bi-clock me-2 text-warning"></i> Kế hoạch chưa hoàn thành:</span>
                        <strong><?php echo $incompletePlans; ?></strong>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-lightning"></i> Hành động nhanh
            </div>
            <div class="card-body">
                <div class="d-grid gap-2 quick-actions">
                    <a href="user_management.php" class="btn btn-outline-primary">
                        <i class="bi bi-people"></i> Quản lý người dùng
                    </a>
                    <a href="../study_plans/plan_list.php" class="btn btn-outline-success">
                        <i class="bi bi-journal-bookmark"></i> Xem tất cả kế hoạch
                    </a>
                    <a href="http://localhost/Baitaplon/handle/logout_process.php" class="btn btn-outline-danger">
                        <i class="bi bi-box-arrow-right"></i> Đăng xuất
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>