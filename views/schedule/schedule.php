<?php
require_once '../../functions/auth.php';
require_once '../../functions/schedule_functions.php';

checkLogin('../../index.php');
$currentUser = getCurrentUser();

// Lấy danh sách thời khóa biểu của người dùng, sắp xếp theo ngày bắt đầu
$conn = getDbConnection();
$sql = "SELECT * FROM schedule WHERE user_id = ? ORDER BY start_date ASC, created_at DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $currentUser['id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$schedules = [];
while ($row = mysqli_fetch_assoc($result)) {
    $schedules[] = $row;
}

mysqli_stmt_close($stmt);
mysqli_close($conn);

// Xử lý thông báo
$successMessage = $_SESSION['success_message'] ?? '';
$errorMessage = $_SESSION['error_message'] ?? '';
$warningMessage = $_SESSION['warning_message'] ?? '';

// Xóa thông báo sau khi hiển thị
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);
unset($_SESSION['warning_message']);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Thời khóa biểu - Hệ Thống Quản Lý Học Tập</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="../../css/dashboard.css" rel="stylesheet">
</head>
<body>
    <?php 
    $currentPage = basename($_SERVER['PHP_SELF']);
    include '../../views/components/header.php'; 
    ?>
    
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-none d-md-block">
                <?php include '../../views/components/sidebar.php'; ?>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <!-- Hiển thị thông báo -->
                <?php if ($successMessage): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($successMessage); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <?php if ($errorMessage): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($errorMessage); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <?php if ($warningMessage): ?>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($warningMessage); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="bg-white p-4 rounded shadow-sm">
                            <h2 class="mb-0">Quản lý Thời khóa biểu</h2>
                            <p class="text-muted mb-0">Tạo và quản lý các thời khóa biểu học tập của bạn</p>
                        </div>
                    </div>
                </div>
                
                <!-- Schedule Management -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span>
                                    <i class="bi bi-calendar-week"></i> Danh sách Thời khóa biểu
                                </span>
                                <a href="create_schedule.php" class="btn btn-sm btn-primary">
                                    <i class="bi bi-plus-lg"></i> Thêm Thời khóa biểu
                                </a>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($schedules)): ?>
                                    <?php foreach ($schedules as $schedule): ?>
                                        <?php 
                                        // Get schedule items for this schedule
                                        $scheduleItems = getScheduleItems($schedule['id']);
                                        
                                        // Tổ chức lại dữ liệu để hiển thị trong bảng
                                        $organizedItems = [];
                                        foreach ($scheduleItems as $item) {
                                            $organizedItems[$item['day_of_week']][$item['time_slot']] = $item;
                                        }
                                        
                                        // Xác định trạng thái thời khóa biểu
                                        $status = '';
                                        $statusClass = '';
                                        $today = new DateTime();
                                        
                                        if ($schedule['start_date']) {
                                            $startDate = new DateTime($schedule['start_date']);
                                            
                                            if ($startDate > $today) {
                                                $status = 'Sử dụng cho tương lai';
                                                $statusClass = 'bg-info';
                                            } else if ($schedule['is_active']) {
                                                $status = 'Đang sử dụng';
                                                $statusClass = 'bg-success';
                                            } else {
                                                $status = 'Ngừng sử dụng';
                                                $statusClass = 'bg-secondary';
                                            }
                                        } else {
                                            if ($schedule['is_active']) {
                                                $status = 'Đang sử dụng';
                                                $statusClass = 'bg-success';
                                            } else {
                                                $status = 'Ngừng sử dụng';
                                                $statusClass = 'bg-secondary';
                                            }
                                        }
                                        ?>
                                        <div class="card mb-4">
                                            <div class="card-header d-flex justify-content-between align-items-center">
                                                <h5 class="mb-0"><?php echo htmlspecialchars($schedule['schedule_name']); ?></h5>
                                                <div class="btn-group" role="group">
                                                    <a href="edit_schedule.php?id=<?php echo $schedule['id']; ?>" class="btn btn-sm btn-outline-warning">
                                                        <i class="bi bi-pencil"></i> Sửa
                                                    </a>
                                                    <a href="../../handle/schedule_process.php?action=delete_schedule&id=<?php echo $schedule['id']; ?>" 
                                                       class="btn btn-sm btn-outline-danger" 
                                                       onclick="return confirm('Bạn có chắc chắn muốn xóa thời khóa biểu này?')">
                                                        <i class="bi bi-trash"></i> Xóa
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <p>
                                                            <strong>Thời gian:</strong> 
                                                            <?php 
                                                            if ($schedule['start_date'] && $schedule['end_date']) {
                                                                echo date('d/m/Y', strtotime($schedule['start_date'])) . ' - ' . date('d/m/Y', strtotime($schedule['end_date']));
                                                            } else {
                                                                echo 'Không xác định';
                                                            }
                                                            ?>
                                                        </p>
                                                        <p>
                                                            <strong>Trạng thái:</strong> 
                                                            <span class="badge <?php echo $statusClass; ?>"><?php echo $status; ?></span>
                                                        </p>
                                                        <p><strong>Số môn học:</strong> <?php echo count($scheduleItems); ?></p>
                                                    </div>
                                                </div>
                                                
                                                <!-- Schedule Table -->
                                                <div class="table-responsive mt-3">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Thời gian</th>
                                                                <th>Thứ Hai</th>
                                                                <th>Thứ Ba</th>
                                                                <th>Thứ Tư</th>
                                                                <th>Thứ Năm</th>
                                                                <th>Thứ Sáu</th>
                                                                <th>Thứ Bảy</th>
                                                                <th>Chủ Nhật</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <!-- Morning -->
                                                            <tr>
                                                                <td>Sáng (7h-11h)</td>
                                                                <td><?php echo isset($organizedItems['monday']['morning']) ? htmlspecialchars($organizedItems['monday']['morning']['plan_title']) : ''; ?></td>
                                                                <td><?php echo isset($organizedItems['tuesday']['morning']) ? htmlspecialchars($organizedItems['tuesday']['morning']['plan_title']) : ''; ?></td>
                                                                <td><?php echo isset($organizedItems['wednesday']['morning']) ? htmlspecialchars($organizedItems['wednesday']['morning']['plan_title']) : ''; ?></td>
                                                                <td><?php echo isset($organizedItems['thursday']['morning']) ? htmlspecialchars($organizedItems['thursday']['morning']['plan_title']) : ''; ?></td>
                                                                <td><?php echo isset($organizedItems['friday']['morning']) ? htmlspecialchars($organizedItems['friday']['morning']['plan_title']) : ''; ?></td>
                                                                <td><?php echo isset($organizedItems['saturday']['morning']) ? htmlspecialchars($organizedItems['saturday']['morning']['plan_title']) : ''; ?></td>
                                                                <td><?php echo isset($organizedItems['sunday']['morning']) ? htmlspecialchars($organizedItems['sunday']['morning']['plan_title']) : ''; ?></td>
                                                            </tr>
                                                            <!-- Afternoon -->
                                                            <tr>
                                                                <td>Chiều (13h-17h)</td>
                                                                <td><?php echo isset($organizedItems['monday']['afternoon']) ? htmlspecialchars($organizedItems['monday']['afternoon']['plan_title']) : ''; ?></td>
                                                                <td><?php echo isset($organizedItems['tuesday']['afternoon']) ? htmlspecialchars($organizedItems['tuesday']['afternoon']['plan_title']) : ''; ?></td>
                                                                <td><?php echo isset($organizedItems['wednesday']['afternoon']) ? htmlspecialchars($organizedItems['wednesday']['afternoon']['plan_title']) : ''; ?></td>
                                                                <td><?php echo isset($organizedItems['thursday']['afternoon']) ? htmlspecialchars($organizedItems['thursday']['afternoon']['plan_title']) : ''; ?></td>
                                                                <td><?php echo isset($organizedItems['friday']['afternoon']) ? htmlspecialchars($organizedItems['friday']['afternoon']['plan_title']) : ''; ?></td>
                                                                <td><?php echo isset($organizedItems['saturday']['afternoon']) ? htmlspecialchars($organizedItems['saturday']['afternoon']['plan_title']) : ''; ?></td>
                                                                <td><?php echo isset($organizedItems['sunday']['afternoon']) ? htmlspecialchars($organizedItems['sunday']['afternoon']['plan_title']) : ''; ?></td>
                                                            </tr>
                                                            <!-- Evening -->
                                                            <tr>
                                                                <td>Tối (19h-21h)</td>
                                                                <td><?php echo isset($organizedItems['monday']['evening']) ? htmlspecialchars($organizedItems['monday']['evening']['plan_title']) : ''; ?></td>
                                                                <td><?php echo isset($organizedItems['tuesday']['evening']) ? htmlspecialchars($organizedItems['tuesday']['evening']['plan_title']) : ''; ?></td>
                                                                <td><?php echo isset($organizedItems['wednesday']['evening']) ? htmlspecialchars($organizedItems['wednesday']['evening']['plan_title']) : ''; ?></td>
                                                                <td><?php echo isset($organizedItems['thursday']['evening']) ? htmlspecialchars($organizedItems['thursday']['evening']['plan_title']) : ''; ?></td>
                                                                <td><?php echo isset($organizedItems['friday']['evening']) ? htmlspecialchars($organizedItems['friday']['evening']['plan_title']) : ''; ?></td>
                                                                <td><?php echo isset($organizedItems['saturday']['evening']) ? htmlspecialchars($organizedItems['saturday']['evening']['plan_title']) : ''; ?></td>
                                                                <td><?php echo isset($organizedItems['sunday']['evening']) ? htmlspecialchars($organizedItems['sunday']['evening']['plan_title']) : ''; ?></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                
                                                <div class="mt-3">
                                                    <a href="view_schedule.php?id=<?php echo $schedule['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-eye"></i> Xem chi tiết
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="text-center py-5">
                                        <i class="bi bi-calendar-x" style="font-size: 3rem; color: #ccc;"></i>
                                        <p class="mt-3">Bạn chưa có thời khóa biểu nào.</p>
                                        <a href="create_schedule.php" class="btn btn-primary">
                                            <i class="bi bi-plus-lg"></i> Tạo thời khóa biểu đầu tiên
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