<?php
require_once dirname(__DIR__) . '/functions/db_connection.php';

/**
 * Lấy danh sách thời khóa biểu của người dùng
 */
function getUserSchedules($userId) {
    $conn = getDbConnection();
    if (!$conn) {
        return [];
    }
    
    $sql = "SELECT * FROM schedule WHERE user_id = ? ORDER BY created_at DESC";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        mysqli_close($conn);
        return [];
    }
    
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $schedules = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $schedules[] = $row;
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $schedules;
}

/**
 * Lấy thông tin chi tiết của một thời khóa biểu
 */
function getScheduleById($scheduleId, $userId) {
    $conn = getDbConnection();
    if (!$conn) {
        return false;
    }
    
    $sql = "SELECT * FROM schedule WHERE id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        mysqli_close($conn);
        return false;
    }
    
    mysqli_stmt_bind_param($stmt, "ii", $scheduleId, $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $schedule = mysqli_fetch_assoc($result);
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $schedule;
}

/**
 * Lấy các môn học trong thời khóa biểu
 */
function getScheduleItems($scheduleId) {
    $conn = getDbConnection();
    if (!$conn) {
        return [];
    }
    
    $sql = "SELECT si.*, sp.title as plan_title FROM schedule_items si 
            LEFT JOIN study_plans sp ON si.study_plan_id = sp.id 
            WHERE si.schedule_id = ? 
            ORDER BY si.day_of_week, si.time_slot";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        mysqli_close($conn);
        return [];
    }
    
    mysqli_stmt_bind_param($stmt, "i", $scheduleId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $items = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $items[] = $row;
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $items;
}

/**
 * Lấy thời khóa biểu đang hoạt động cho ngày hôm nay
 */
function getActiveScheduleForToday($userId) {
    $conn = getDbConnection();
    if (!$conn) {
        return false;
    }
    
    // Lấy ngày hôm nay
    $today = date('Y-m-d');
    
    // Lấy thời khóa biểu đang hoạt động và có ngày bắt đầu <= hôm nay và (ngày kết thúc >= hôm nay hoặc không có ngày kết thúc)
    $sql = "SELECT * FROM schedule 
            WHERE user_id = ? 
            AND is_active = 1 
            AND start_date <= ? 
            AND (end_date >= ? OR end_date IS NULL)
            ORDER BY start_date DESC 
            LIMIT 1";
    
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        mysqli_close($conn);
        return false;
    }
    
    mysqli_stmt_bind_param($stmt, "iss", $userId, $today, $today);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $schedule = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    if ($schedule) {
        // Lấy các môn học trong thời khóa biểu
        $schedule['items'] = getScheduleItems($schedule['id']);
    }
    
    mysqli_close($conn);
    return $schedule;
}

/**
 * Lấy danh sách kế hoạch học tập của người dùng
 */
function getUserStudyPlansForSchedule($userId) {
    $conn = getDbConnection();
    if (!$conn) {
        return [];
    }
    
    $sql = "SELECT id, title FROM study_plans WHERE user_id = ? ORDER BY title";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        mysqli_close($conn);
        return [];
    }
    
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $plans = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $plans[] = $row;
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $plans;
}

/**
 * Tạo thời khóa biểu mới
 */
function createSchedule($userId, $scheduleName, $startDate = null, $endDate = null, $isActive = 1) {
    $conn = getDbConnection();
    if (!$conn) {
        return false;
    }
    
    // Kiểm tra xem user_id có tồn tại không
    $checkUserSql = "SELECT id FROM users WHERE id = ?";
    $checkUserStmt = mysqli_prepare($conn, $checkUserSql);
    if (!$checkUserStmt) {
        mysqli_close($conn);
        return false;
    }
    
    mysqli_stmt_bind_param($checkUserStmt, "i", $userId);
    mysqli_stmt_execute($checkUserStmt);
    $userResult = mysqli_stmt_get_result($checkUserStmt);
    
    if (mysqli_num_rows($userResult) == 0) {
        mysqli_stmt_close($checkUserStmt);
        mysqli_close($conn);
        return false;
    }
    
    mysqli_stmt_close($checkUserStmt);
    
    $sql = "INSERT INTO schedule (user_id, schedule_name, start_date, end_date, is_active) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        mysqli_close($conn);
        return false;
    }
    
    // Xử lý trường hợp startDate và endDate là chuỗi rỗng
    if ($startDate === '') $startDate = null;
    if ($endDate === '') $endDate = null;
    
    mysqli_stmt_bind_param($stmt, "isssi", $userId, $scheduleName, $startDate, $endDate, $isActive);
    $result = mysqli_stmt_execute($stmt);
    
    if ($result) {
        $scheduleId = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $scheduleId;
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return false;
}

/**
 * Cập nhật thông tin thời khóa biểu
 */
function updateSchedule($scheduleId, $userId, $scheduleName, $startDate = null, $endDate = null, $isActive = 1) {
    $conn = getDbConnection();
    if (!$conn) {
        return false;
    }
    
    // Kiểm tra xem thời khóa biểu có tồn tại và thuộc về user không
    $checkScheduleSql = "SELECT id FROM schedule WHERE id = ? AND user_id = ?";
    $checkScheduleStmt = mysqli_prepare($conn, $checkScheduleSql);
    if (!$checkScheduleStmt) {
        mysqli_close($conn);
        return false;
    }
    
    mysqli_stmt_bind_param($checkScheduleStmt, "ii", $scheduleId, $userId);
    mysqli_stmt_execute($checkScheduleStmt);
    $scheduleResult = mysqli_stmt_get_result($checkScheduleStmt);
    
    if (mysqli_num_rows($scheduleResult) == 0) {
        mysqli_stmt_close($checkScheduleStmt);
        mysqli_close($conn);
        return false;
    }
    
    mysqli_stmt_close($checkScheduleStmt);
    
    $sql = "UPDATE schedule SET schedule_name = ?, start_date = ?, end_date = ?, is_active = ? WHERE id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        mysqli_close($conn);
        return false;
    }
    
    // Xử lý trường hợp startDate và endDate là chuỗi rỗng
    if ($startDate === '') $startDate = null;
    if ($endDate === '') $endDate = null;
    
    // Sửa lỗi thứ tự tham số bind - phải khớp với thứ tự trong câu SQL
    mysqli_stmt_bind_param($stmt, "sssiii", $scheduleName, $startDate, $endDate, $isActive, $scheduleId, $userId);
    $result = mysqli_stmt_execute($stmt);
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    // Trả về true nếu thành công
    return $result;
}

/**
 * Thêm môn học vào thời khóa biểu
 */
function addScheduleItem($scheduleId, $studyPlanId, $dayOfWeek, $timeSlot) {
    $conn = getDbConnection();
    if (!$conn) {
        return false;
    }
    
    // Kiểm tra xem thời khóa biểu có tồn tại không
    $checkScheduleSql = "SELECT id FROM schedule WHERE id = ?";
    $checkScheduleStmt = mysqli_prepare($conn, $checkScheduleSql);
    if (!$checkScheduleStmt) {
        mysqli_close($conn);
        return false;
    }
    
    mysqli_stmt_bind_param($checkScheduleStmt, "i", $scheduleId);
    mysqli_stmt_execute($checkScheduleStmt);
    $scheduleResult = mysqli_stmt_get_result($checkScheduleStmt);
    
    if (mysqli_num_rows($scheduleResult) == 0) {
        mysqli_stmt_close($checkScheduleStmt);
        mysqli_close($conn);
        return false;
    }
    
    mysqli_stmt_close($checkScheduleStmt);
    
    // Kiểm tra xem kế hoạch học tập có tồn tại không
    $checkPlanSql = "SELECT id FROM study_plans WHERE id = ?";
    $checkPlanStmt = mysqli_prepare($conn, $checkPlanSql);
    if (!$checkPlanStmt) {
        mysqli_close($conn);
        return false;
    }
    
    mysqli_stmt_bind_param($checkPlanStmt, "i", $studyPlanId);
    mysqli_stmt_execute($checkPlanStmt);
    $planResult = mysqli_stmt_get_result($checkPlanStmt);
    
    if (mysqli_num_rows($planResult) == 0) {
        mysqli_stmt_close($checkPlanStmt);
        mysqli_close($conn);
        return false;
    }
    
    mysqli_stmt_close($checkPlanStmt);
    
    $sql = "INSERT INTO schedule_items (schedule_id, study_plan_id, day_of_week, time_slot) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        mysqli_close($conn);
        return false;
    }
    
    mysqli_stmt_bind_param($stmt, "iiss", $scheduleId, $studyPlanId, $dayOfWeek, $timeSlot);
    $result = mysqli_stmt_execute($stmt);
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $result;
}

/**
 * Xóa tất cả môn học trong thời khóa biểu
 */
function deleteAllScheduleItems($scheduleId, $userId) {
    $conn = getDbConnection();
    if (!$conn) {
        return false;
    }
    
    // Kiểm tra xem thời khóa biểu có tồn tại và thuộc về user không
    $checkScheduleSql = "SELECT id FROM schedule WHERE id = ? AND user_id = ?";
    $checkScheduleStmt = mysqli_prepare($conn, $checkScheduleSql);
    if (!$checkScheduleStmt) {
        mysqli_close($conn);
        return false;
    }
    
    mysqli_stmt_bind_param($checkScheduleStmt, "ii", $scheduleId, $userId);
    mysqli_stmt_execute($checkScheduleStmt);
    $scheduleResult = mysqli_stmt_get_result($checkScheduleStmt);
    
    if (mysqli_num_rows($scheduleResult) == 0) {
        mysqli_stmt_close($checkScheduleStmt);
        mysqli_close($conn);
        return false;
    }
    
    mysqli_stmt_close($checkScheduleStmt);
    
    // Sửa lỗi SQL - không cần JOIN để xóa các môn học trong thời khóa biểu
    $sql = "DELETE FROM schedule_items WHERE schedule_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        mysqli_close($conn);
        return false;
    }
    
    mysqli_stmt_bind_param($stmt, "i", $scheduleId);
    $result = mysqli_stmt_execute($stmt);
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $result;
}

/**
 * Xóa thời khóa biểu
 */
function deleteSchedule($scheduleId, $userId) {
    $conn = getDbConnection();
    if (!$conn) {
        return false;
    }
    
    $sql = "DELETE FROM schedule WHERE id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        mysqli_close($conn);
        return false;
    }
    
    mysqli_stmt_bind_param($stmt, "ii", $scheduleId, $userId);
    $result = mysqli_stmt_execute($stmt);
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $result;
}

/**
 * Xóa môn học khỏi thời khóa biểu
 */
function deleteScheduleItem($itemId, $userId) {
    $conn = getDbConnection();
    if (!$conn) {
        return false;
    }
    
    // Kiểm tra xem item có tồn tại và thuộc về user không
    $checkItemSql = "SELECT si.id FROM schedule_items si 
                     JOIN schedule s ON si.schedule_id = s.id 
                     WHERE si.id = ? AND s.user_id = ?";
    $checkItemStmt = mysqli_prepare($conn, $checkItemSql);
    if (!$checkItemStmt) {
        mysqli_close($conn);
        return false;
    }
    
    mysqli_stmt_bind_param($checkItemStmt, "ii", $itemId, $userId);
    mysqli_stmt_execute($checkItemStmt);
    $itemResult = mysqli_stmt_get_result($checkItemStmt);
    
    if (mysqli_num_rows($itemResult) == 0) {
        mysqli_stmt_close($checkItemStmt);
        mysqli_close($conn);
        return false;
    }
    
    mysqli_stmt_close($checkItemStmt);
    
    // Sửa lỗi SQL - chỉ cần xóa item theo ID
    $sql = "DELETE FROM schedule_items WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        mysqli_close($conn);
        return false;
    }
    
    mysqli_stmt_bind_param($stmt, "i", $itemId);
    $result = mysqli_stmt_execute($stmt);
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $result;
}