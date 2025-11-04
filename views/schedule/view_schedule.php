<?php
require_once '../../functions/auth.php';
require_once '../../functions/schedule_functions.php';

checkLogin('../../index.php');
$currentUser = getCurrentUser();

// Lấy thông tin thời khóa biểu
$scheduleId = $_GET['id'] ?? 0;
$schedule = getScheduleById($scheduleId, $currentUser['id']);

if (!$schedule) {
    header('Location: schedule.php');
    exit();
}

$studyPlans = getUserStudyPlansForSchedule($currentUser['id']);
$scheduleItems = getScheduleItems($scheduleId);

// Tổ chức lại dữ liệu để hiển thị trong bảng
$organizedItems = [];
foreach ($scheduleItems as $item) {
    $organizedItems[$item['day_of_week']][$item['time_slot']] = $item;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($schedule['schedule_name']); ?> - Hệ Thống Quản Lý Học Tập</title>
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
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="bg-white p-4 rounded shadow-sm">
                            <h2 class="mb-0"><?php echo htmlspecialchars($schedule['schedule_name']); ?></h2>
                            <p class="text-muted mb-0">
                                <?php 
                                if ($schedule['start_date'] && $schedule['end_date']) {
                                    echo 'Từ ' . date('d/m/Y', strtotime($schedule['start_date'])) . ' đến ' . date('d/m/Y', strtotime($schedule['end_date']));
                                } else {
                                    echo 'Thời gian không xác định';
                                }
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Schedule Content -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span>
                                    <i class="bi bi-calendar-week"></i> Thời khóa biểu
                                </span>
                                <div class="btn-group" role="group">
                                    <a href="edit_schedule.php?id=<?php echo $schedule['id']; ?>" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i> Sửa thời khóa biểu
                                    </a>
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addScheduleItemModal">
                                        <i class="bi bi-plus-lg"></i> Thêm môn học
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <!-- Schedule Table -->
                                <div class="table-responsive">
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
                                                <td><?php echo isset($organizedItems['monday']['morning']) ? htmlspecialchars($organizedItems['monday']['morning']['plan_title']) . ' <a href="../../handle/schedule_process.php?action=delete_schedule_item&id=' . $organizedItems['monday']['morning']['id'] . '&schedule_id=' . $scheduleId . '" class="text-danger" onclick="return confirm(\'Bạn có chắc chắn muốn xóa môn học này?\')"><i class="bi bi-x-circle"></i></a>' : ''; ?></td>
                                                <td><?php echo isset($organizedItems['tuesday']['morning']) ? htmlspecialchars($organizedItems['tuesday']['morning']['plan_title']) . ' <a href="../../handle/schedule_process.php?action=delete_schedule_item&id=' . $organizedItems['tuesday']['morning']['id'] . '&schedule_id=' . $scheduleId . '" class="text-danger" onclick="return confirm(\'Bạn có chắc chắn muốn xóa môn học này?\')"><i class="bi bi-x-circle"></i></a>' : ''; ?></td>
                                                <td><?php echo isset($organizedItems['wednesday']['morning']) ? htmlspecialchars($organizedItems['wednesday']['morning']['plan_title']) . ' <a href="../../handle/schedule_process.php?action=delete_schedule_item&id=' . $organizedItems['wednesday']['morning']['id'] . '&schedule_id=' . $scheduleId . '" class="text-danger" onclick="return confirm(\'Bạn có chắc chắn muốn xóa môn học này?\')"><i class="bi bi-x-circle"></i></a>' : ''; ?></td>
                                                <td><?php echo isset($organizedItems['thursday']['morning']) ? htmlspecialchars($organizedItems['thursday']['morning']['plan_title']) . ' <a href="../../handle/schedule_process.php?action=delete_schedule_item&id=' . $organizedItems['thursday']['morning']['id'] . '&schedule_id=' . $scheduleId . '" class="text-danger" onclick="return confirm(\'Bạn có chắc chắn muốn xóa môn học này?\')"><i class="bi bi-x-circle"></i></a>' : ''; ?></td>
                                                <td><?php echo isset($organizedItems['friday']['morning']) ? htmlspecialchars($organizedItems['friday']['morning']['plan_title']) . ' <a href="../../handle/schedule_process.php?action=delete_schedule_item&id=' . $organizedItems['friday']['morning']['id'] . '&schedule_id=' . $scheduleId . '" class="text-danger" onclick="return confirm(\'Bạn có chắc chắn muốn xóa môn học này?\')"><i class="bi bi-x-circle"></i></a>' : ''; ?></td>
                                                <td><?php echo isset($organizedItems['saturday']['morning']) ? htmlspecialchars($organizedItems['saturday']['morning']['plan_title']) . ' <a href="../../handle/schedule_process.php?action=delete_schedule_item&id=' . $organizedItems['saturday']['morning']['id'] . '&schedule_id=' . $scheduleId . '" class="text-danger" onclick="return confirm(\'Bạn có chắc chắn muốn xóa môn học này?\')"><i class="bi bi-x-circle"></i></a>' : ''; ?></td>
                                                <td><?php echo isset($organizedItems['sunday']['morning']) ? htmlspecialchars($organizedItems['sunday']['morning']['plan_title']) . ' <a href="../../handle/schedule_process.php?action=delete_schedule_item&id=' . $organizedItems['sunday']['morning']['id'] . '&schedule_id=' . $scheduleId . '" class="text-danger" onclick="return confirm(\'Bạn có chắc chắn muốn xóa môn học này?\')"><i class="bi bi-x-circle"></i></a>' : ''; ?></td>
                                            </tr>
                                            <!-- Afternoon -->
                                            <tr>
                                                <td>Chiều (13h-17h)</td>
                                                <td><?php echo isset($organizedItems['monday']['afternoon']) ? htmlspecialchars($organizedItems['monday']['afternoon']['plan_title']) . ' <a href="../../handle/schedule_process.php?action=delete_schedule_item&id=' . $organizedItems['monday']['afternoon']['id'] . '&schedule_id=' . $scheduleId . '" class="text-danger" onclick="return confirm(\'Bạn có chắc chắn muốn xóa môn học này?\')"><i class="bi bi-x-circle"></i></a>' : ''; ?></td>
                                                <td><?php echo isset($organizedItems['tuesday']['afternoon']) ? htmlspecialchars($organizedItems['tuesday']['afternoon']['plan_title']) . ' <a href="../../handle/schedule_process.php?action=delete_schedule_item&id=' . $organizedItems['tuesday']['afternoon']['id'] . '&schedule_id=' . $scheduleId . '" class="text-danger" onclick="return confirm(\'Bạn có chắc chắn muốn xóa môn học này?\')"><i class="bi bi-x-circle"></i></a>' : ''; ?></td>
                                                <td><?php echo isset($organizedItems['wednesday']['afternoon']) ? htmlspecialchars($organizedItems['wednesday']['afternoon']['plan_title']) . ' <a href="../../handle/schedule_process.php?action=delete_schedule_item&id=' . $organizedItems['wednesday']['afternoon']['id'] . '&schedule_id=' . $scheduleId . '" class="text-danger" onclick="return confirm(\'Bạn có chắc chắn muốn xóa môn học này?\')"><i class="bi bi-x-circle"></i></a>' : ''; ?></td>
                                                <td><?php echo isset($organizedItems['thursday']['afternoon']) ? htmlspecialchars($organizedItems['thursday']['afternoon']['plan_title']) . ' <a href="../../handle/schedule_process.php?action=delete_schedule_item&id=' . $organizedItems['thursday']['afternoon']['id'] . '&schedule_id=' . $scheduleId . '" class="text-danger" onclick="return confirm(\'Bạn có chắc chắn muốn xóa môn học này?\')"><i class="bi bi-x-circle"></i></a>' : ''; ?></td>
                                                <td><?php echo isset($organizedItems['friday']['afternoon']) ? htmlspecialchars($organizedItems['friday']['afternoon']['plan_title']) . ' <a href="../../handle/schedule_process.php?action=delete_schedule_item&id=' . $organizedItems['friday']['afternoon']['id'] . '&schedule_id=' . $scheduleId . '" class="text-danger" onclick="return confirm(\'Bạn có chắc chắn muốn xóa môn học này?\')"><i class="bi bi-x-circle"></i></a>' : ''; ?></td>
                                                <td><?php echo isset($organizedItems['saturday']['afternoon']) ? htmlspecialchars($organizedItems['saturday']['afternoon']['plan_title']) . ' <a href="../../handle/schedule_process.php?action=delete_schedule_item&id=' . $organizedItems['saturday']['afternoon']['id'] . '&schedule_id=' . $scheduleId . '" class="text-danger" onclick="return confirm(\'Bạn có chắc chắn muốn xóa môn học này?\')"><i class="bi bi-x-circle"></i></a>' : ''; ?></td>
                                                <td><?php echo isset($organizedItems['sunday']['afternoon']) ? htmlspecialchars($organizedItems['sunday']['afternoon']['plan_title']) . ' <a href="../../handle/schedule_process.php?action=delete_schedule_item&id=' . $organizedItems['sunday']['afternoon']['id'] . '&schedule_id=' . $scheduleId . '" class="text-danger" onclick="return confirm(\'Bạn có chắc chắn muốn xóa môn học này?\')"><i class="bi bi-x-circle"></i></a>' : ''; ?></td>
                                            </tr>
                                            <!-- Evening -->
                                            <tr>
                                                <td>Tối (19h-21h)</td>
                                                <td><?php echo isset($organizedItems['monday']['evening']) ? htmlspecialchars($organizedItems['monday']['evening']['plan_title']) . ' <a href="../../handle/schedule_process.php?action=delete_schedule_item&id=' . $organizedItems['monday']['evening']['id'] . '&schedule_id=' . $scheduleId . '" class="text-danger" onclick="return confirm(\'Bạn có chắc chắn muốn xóa môn học này?\')"><i class="bi bi-x-circle"></i></a>' : ''; ?></td>
                                                <td><?php echo isset($organizedItems['tuesday']['evening']) ? htmlspecialchars($organizedItems['tuesday']['evening']['plan_title']) . ' <a href="../../handle/schedule_process.php?action=delete_schedule_item&id=' . $organizedItems['tuesday']['evening']['id'] . '&schedule_id=' . $scheduleId . '" class="text-danger" onclick="return confirm(\'Bạn có chắc chắn muốn xóa môn học này?\')"><i class="bi bi-x-circle"></i></a>' : ''; ?></td>
                                                <td><?php echo isset($organizedItems['wednesday']['evening']) ? htmlspecialchars($organizedItems['wednesday']['evening']['plan_title']) . ' <a href="../../handle/schedule_process.php?action=delete_schedule_item&id=' . $organizedItems['wednesday']['evening']['id'] . '&schedule_id=' . $scheduleId . '" class="text-danger" onclick="return confirm(\'Bạn có chắc chắn muốn xóa môn học này?\')"><i class="bi bi-x-circle"></i></a>' : ''; ?></td>
                                                <td><?php echo isset($organizedItems['thursday']['evening']) ? htmlspecialchars($organizedItems['thursday']['evening']['plan_title']) . ' <a href="../../handle/schedule_process.php?action=delete_schedule_item&id=' . $organizedItems['thursday']['evening']['id'] . '&schedule_id=' . $scheduleId . '" class="text-danger" onclick="return confirm(\'Bạn có chắc chắn muốn xóa môn học này?\')"><i class="bi bi-x-circle"></i></a>' : ''; ?></td>
                                                <td><?php echo isset($organizedItems['friday']['evening']) ? htmlspecialchars($organizedItems['friday']['evening']['plan_title']) . ' <a href="../../handle/schedule_process.php?action=delete_schedule_item&id=' . $organizedItems['friday']['evening']['id'] . '&schedule_id=' . $scheduleId . '" class="text-danger" onclick="return confirm(\'Bạn có chắc chắn muốn xóa môn học này?\')"><i class="bi bi-x-circle"></i></a>' : ''; ?></td>
                                                <td><?php echo isset($organizedItems['saturday']['evening']) ? htmlspecialchars($organizedItems['saturday']['evening']['plan_title']) . ' <a href="../../handle/schedule_process.php?action=delete_schedule_item&id=' . $organizedItems['saturday']['evening']['id'] . '&schedule_id=' . $scheduleId . '" class="text-danger" onclick="return confirm(\'Bạn có chắc chắn muốn xóa môn học này?\')"><i class="bi bi-x-circle"></i></a>' : ''; ?></td>
                                                <td><?php echo isset($organizedItems['sunday']['evening']) ? htmlspecialchars($organizedItems['sunday']['evening']['plan_title']) . ' <a href="../../handle/schedule_process.php?action=delete_schedule_item&id=' . $organizedItems['sunday']['evening']['id'] . '&schedule_id=' . $scheduleId . '" class="text-danger" onclick="return confirm(\'Bạn có chắc chắn muốn xóa môn học này?\')"><i class="bi bi-x-circle"></i></a>' : ''; ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Schedule Item Modal -->
    <div class="modal fade" id="addScheduleItemModal" tabindex="-1" aria-labelledby="addScheduleItemModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="../../handle/schedule_process.php" method="POST">
                    <input type="hidden" name="action" value="add_schedule_item">
                    <input type="hidden" name="schedule_id" value="<?php echo $scheduleId; ?>">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addScheduleItemModalLabel">Thêm môn học vào thời khóa biểu</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="study_plan_id" class="form-label">Chọn kế hoạch học tập</label>
                            <select class="form-select" id="study_plan_id" name="study_plan_id" required>
                                <option value="">-- Chọn kế hoạch học tập --</option>
                                <?php foreach ($studyPlans as $plan): ?>
                                    <option value="<?php echo $plan['id']; ?>">
                                        <?php echo htmlspecialchars($plan['title']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="day_of_week" class="form-label">Chọn ngày trong tuần</label>
                            <select class="form-select" id="day_of_week" name="day_of_week" required>
                                <option value="">-- Chọn ngày --</option>
                                <option value="monday">Thứ Hai</option>
                                <option value="tuesday">Thứ Ba</option>
                                <option value="wednesday">Thứ Tư</option>
                                <option value="thursday">Thứ Năm</option>
                                <option value="friday">Thứ Sáu</option>
                                <option value="saturday">Thứ Bảy</option>
                                <option value="sunday">Chủ Nhật</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="time_slot" class="form-label">Chọn thời gian</label>
                            <select class="form-select" id="time_slot" name="time_slot" required>
                                <option value="">-- Chọn thời gian --</option>
                                <option value="morning">Sáng (7h-11h)</option>
                                <option value="afternoon">Chiều (13h-17h)</option>
                                <option value="evening">Tối (19h-21h)</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Thêm vào thời khóa biểu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>