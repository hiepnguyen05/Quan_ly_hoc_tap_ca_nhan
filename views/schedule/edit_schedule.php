<?php
require_once '../../functions/auth.php';
require_once '../../functions/schedule_functions.php';

checkLogin('../../index.php');
$currentUser = getCurrentUser();

// Lấy thông tin thời khóa biểu
$scheduleId = $_GET['id'] ?? 0;
$schedule = getScheduleById($scheduleId, $currentUser['id']);

if (!$schedule) {
    $_SESSION['error_message'] = 'Không tìm thấy thời khóa biểu.';
    header('Location: schedule.php');
    exit();
}

// Lấy danh sách kế hoạch học tập của người dùng
$studyPlans = getUserStudyPlansForSchedule($currentUser['id']);

// Lấy các môn học trong thời khóa biểu
$scheduleItems = getScheduleItems($scheduleId);

// Tổ chức lại dữ liệu để hiển thị trong bảng
$organizedItems = [];
foreach ($scheduleItems as $item) {
    $organizedItems[$item['day_of_week']][$item['time_slot']] = $item;
}

// Xử lý thông báo
$errorMessage = $_SESSION['error_message'] ?? '';
$successMessage = $_SESSION['success_message'] ?? '';
$warningMessage = $_SESSION['warning_message'] ?? '';

// Xóa thông báo sau khi hiển thị
unset($_SESSION['error_message']);
unset($_SESSION['success_message']);
unset($_SESSION['warning_message']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa Thời khóa biểu - Hệ Thống Quản Lý Học Tập</title>
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
                <!-- Hiển thị thông báo lỗi -->
                <?php if (!empty($errorMessage)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($errorMessage); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($successMessage)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($successMessage); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($warningMessage)): ?>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($warningMessage); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="bg-white p-4 rounded shadow-sm">
                            <h2 class="mb-0">Sửa Thời khóa biểu</h2>
                            <p class="text-muted mb-0">Chỉnh sửa thông tin thời khóa biểu</p>
                        </div>
                    </div>
                </div>
                
                <!-- Edit Schedule Form -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <i class="bi bi-calendar-plus"></i> Thông tin Thời khóa biểu
                            </div>
                            <div class="card-body">
                                <form action="../../handle/schedule_process.php" method="POST">
                                    <input type="hidden" name="action" value="edit_schedule">
                                    <input type="hidden" name="schedule_id" value="<?php echo $schedule['id']; ?>">
                                    
                                    <div class="mb-3">
                                        <label for="schedule_name" class="form-label">Tên Thời khóa biểu</label>
                                        <input type="text" class="form-control" id="schedule_name" name="schedule_name" value="<?php echo htmlspecialchars($schedule['schedule_name']); ?>" required>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="start_date" class="form-label">Ngày bắt đầu</label>
                                                <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $schedule['start_date']; ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="end_date" class="form-label">Ngày kết thúc</label>
                                                <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $schedule['end_date']; ?>">
                                            </div>
                                        </div>
                    </div>
                                    
                    <div class="mb-3">
                        <label for="is_active" class="form-label">Trạng thái</label>
                        <select class="form-select" id="is_active" name="is_active">
                            <option value="1" <?php echo $schedule['is_active'] ? 'selected' : ''; ?>>Đang sử dụng</option>
                            <option value="0" <?php echo !$schedule['is_active'] ? 'selected' : ''; ?>>Ngừng sử dụng</option>
                        </select>
                        <div class="form-text">Nếu ngày bắt đầu là tương lai, thời khóa biểu sẽ tự động được đánh dấu là "Sử dụng cho tương lai"</div>
                    </div>
                                    
                    <!-- Schedule Items Section -->
                    <div class="mb-3">
                        <label class="form-label"><strong>Sửa môn học trong thời khóa biểu</strong></label>
                        
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
                                        <td>
                                            <select class="form-select form-select-sm" name="schedule_items[monday][morning]">
                                                <option value="">-- Chọn môn học --</option>
                                                <?php foreach ($studyPlans as $plan): ?>
                                                    <option value="<?php echo $plan['id']; ?>" <?php echo (isset($organizedItems['monday']['morning']) && $organizedItems['monday']['morning']['study_plan_id'] == $plan['id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($plan['title']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-select form-select-sm" name="schedule_items[tuesday][morning]">
                                                <option value="">-- Chọn môn học --</option>
                                                <?php foreach ($studyPlans as $plan): ?>
                                                    <option value="<?php echo $plan['id']; ?>" <?php echo (isset($organizedItems['tuesday']['morning']) && $organizedItems['tuesday']['morning']['study_plan_id'] == $plan['id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($plan['title']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-select form-select-sm" name="schedule_items[wednesday][morning]">
                                                <option value="">-- Chọn môn học --</option>
                                                <?php foreach ($studyPlans as $plan): ?>
                                                    <option value="<?php echo $plan['id']; ?>" <?php echo (isset($organizedItems['wednesday']['morning']) && $organizedItems['wednesday']['morning']['study_plan_id'] == $plan['id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($plan['title']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-select form-select-sm" name="schedule_items[thursday][morning]">
                                                <option value="">-- Chọn môn học --</option>
                                                <?php foreach ($studyPlans as $plan): ?>
                                                    <option value="<?php echo $plan['id']; ?>" <?php echo (isset($organizedItems['thursday']['morning']) && $organizedItems['thursday']['morning']['study_plan_id'] == $plan['id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($plan['title']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-select form-select-sm" name="schedule_items[friday][morning]">
                                                <option value="">-- Chọn môn học --</option>
                                                <?php foreach ($studyPlans as $plan): ?>
                                                    <option value="<?php echo $plan['id']; ?>" <?php echo (isset($organizedItems['friday']['morning']) && $organizedItems['friday']['morning']['study_plan_id'] == $plan['id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($plan['title']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-select form-select-sm" name="schedule_items[saturday][morning]">
                                                <option value="">-- Chọn môn học --</option>
                                                <?php foreach ($studyPlans as $plan): ?>
                                                    <option value="<?php echo $plan['id']; ?>" <?php echo (isset($organizedItems['saturday']['morning']) && $organizedItems['saturday']['morning']['study_plan_id'] == $plan['id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($plan['title']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-select form-select-sm" name="schedule_items[sunday][morning]">
                                                <option value="">-- Chọn môn học --</option>
                                                <?php foreach ($studyPlans as $plan): ?>
                                                    <option value="<?php echo $plan['id']; ?>" <?php echo (isset($organizedItems['sunday']['morning']) && $organizedItems['sunday']['morning']['study_plan_id'] == $plan['id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($plan['title']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <!-- Afternoon -->
                                    <tr>
                                        <td>Chiều (13h-17h)</td>
                                        <td>
                                            <select class="form-select form-select-sm" name="schedule_items[monday][afternoon]">
                                                <option value="">-- Chọn môn học --</option>
                                                <?php foreach ($studyPlans as $plan): ?>
                                                    <option value="<?php echo $plan['id']; ?>" <?php echo (isset($organizedItems['monday']['afternoon']) && $organizedItems['monday']['afternoon']['study_plan_id'] == $plan['id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($plan['title']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-select form-select-sm" name="schedule_items[tuesday][afternoon]">
                                                <option value="">-- Chọn môn học --</option>
                                                <?php foreach ($studyPlans as $plan): ?>
                                                    <option value="<?php echo $plan['id']; ?>" <?php echo (isset($organizedItems['tuesday']['afternoon']) && $organizedItems['tuesday']['afternoon']['study_plan_id'] == $plan['id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($plan['title']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-select form-select-sm" name="schedule_items[wednesday][afternoon]">
                                                <option value="">-- Chọn môn học --</option>
                                                <?php foreach ($studyPlans as $plan): ?>
                                                    <option value="<?php echo $plan['id']; ?>" <?php echo (isset($organizedItems['wednesday']['afternoon']) && $organizedItems['wednesday']['afternoon']['study_plan_id'] == $plan['id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($plan['title']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-select form-select-sm" name="schedule_items[thursday][afternoon]">
                                                <option value="">-- Chọn môn học --</option>
                                                <?php foreach ($studyPlans as $plan): ?>
                                                    <option value="<?php echo $plan['id']; ?>" <?php echo (isset($organizedItems['thursday']['afternoon']) && $organizedItems['thursday']['afternoon']['study_plan_id'] == $plan['id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($plan['title']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-select form-select-sm" name="schedule_items[friday][afternoon]">
                                                <option value="">-- Chọn môn học --</option>
                                                <?php foreach ($studyPlans as $plan): ?>
                                                    <option value="<?php echo $plan['id']; ?>" <?php echo (isset($organizedItems['friday']['afternoon']) && $organizedItems['friday']['afternoon']['study_plan_id'] == $plan['id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($plan['title']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-select form-select-sm" name="schedule_items[saturday][afternoon]">
                                                <option value="">-- Chọn môn học --</option>
                                                <?php foreach ($studyPlans as $plan): ?>
                                                    <option value="<?php echo $plan['id']; ?>" <?php echo (isset($organizedItems['saturday']['afternoon']) && $organizedItems['saturday']['afternoon']['study_plan_id'] == $plan['id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($plan['title']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-select form-select-sm" name="schedule_items[sunday][afternoon]">
                                                <option value="">-- Chọn môn học --</option>
                                                <?php foreach ($studyPlans as $plan): ?>
                                                    <option value="<?php echo $plan['id']; ?>" <?php echo (isset($organizedItems['sunday']['afternoon']) && $organizedItems['sunday']['afternoon']['study_plan_id'] == $plan['id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($plan['title']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <!-- Evening -->
                                    <tr>
                                        <td>Tối (19h-21h)</td>
                                        <td>
                                            <select class="form-select form-select-sm" name="schedule_items[monday][evening]">
                                                <option value="">-- Chọn môn học --</option>
                                                <?php foreach ($studyPlans as $plan): ?>
                                                    <option value="<?php echo $plan['id']; ?>" <?php echo (isset($organizedItems['monday']['evening']) && $organizedItems['monday']['evening']['study_plan_id'] == $plan['id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($plan['title']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-select form-select-sm" name="schedule_items[tuesday][evening]">
                                                <option value="">-- Chọn môn học --</option>
                                                <?php foreach ($studyPlans as $plan): ?>
                                                    <option value="<?php echo $plan['id']; ?>" <?php echo (isset($organizedItems['tuesday']['evening']) && $organizedItems['tuesday']['evening']['study_plan_id'] == $plan['id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($plan['title']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-select form-select-sm" name="schedule_items[wednesday][evening]">
                                                <option value="">-- Chọn môn học --</option>
                                                <?php foreach ($studyPlans as $plan): ?>
                                                    <option value="<?php echo $plan['id']; ?>" <?php echo (isset($organizedItems['wednesday']['evening']) && $organizedItems['wednesday']['evening']['study_plan_id'] == $plan['id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($plan['title']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-select form-select-sm" name="schedule_items[thursday][evening]">
                                                <option value="">-- Chọn môn học --</option>
                                                <?php foreach ($studyPlans as $plan): ?>
                                                    <option value="<?php echo $plan['id']; ?>" <?php echo (isset($organizedItems['thursday']['evening']) && $organizedItems['thursday']['evening']['study_plan_id'] == $plan['id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($plan['title']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-select form-select-sm" name="schedule_items[friday][evening]">
                                                <option value="">-- Chọn môn học --</option>
                                                <?php foreach ($studyPlans as $plan): ?>
                                                    <option value="<?php echo $plan['id']; ?>" <?php echo (isset($organizedItems['friday']['evening']) && $organizedItems['friday']['evening']['study_plan_id'] == $plan['id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($plan['title']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-select form-select-sm" name="schedule_items[saturday][evening]">
                                                <option value="">-- Chọn môn học --</option>
                                                <?php foreach ($studyPlans as $plan): ?>
                                                    <option value="<?php echo $plan['id']; ?>" <?php echo (isset($organizedItems['saturday']['evening']) && $organizedItems['saturday']['evening']['study_plan_id'] == $plan['id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($plan['title']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-select form-select-sm" name="schedule_items[sunday][evening]">
                                                <option value="">-- Chọn môn học --</option>
                                                <?php foreach ($studyPlans as $plan): ?>
                                                    <option value="<?php echo $plan['id']; ?>" <?php echo (isset($organizedItems['sunday']['evening']) && $organizedItems['sunday']['evening']['study_plan_id'] == $plan['id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($plan['title']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                                    
                    <div class="d-flex justify-content-between">
                        <a href="schedule.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Quay lại
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Cập nhật Thời khóa biểu
                        </button>
                    </div>
                </form>
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