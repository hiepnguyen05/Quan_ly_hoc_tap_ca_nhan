<?php
require_once dirname(__DIR__) . '/functions/db_connection.php';

/**
 * Lấy danh sách thời khóa biểu của người dùng
 */
function getUserSchedules($userId) {
    $conn = getDbConnection();
    $sql = "SELECT * FROM schedule WHERE user_id = ? ORDER BY created_at DESC";
    $stmt = mysqli_prepare($conn, $sql);
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
    $sql = "SELECT * FROM schedule WHERE id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
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
    $sql = "SELECT si.*, sp.title as plan_title FROM schedule_items si 
            LEFT JOIN study_plans sp ON si.study_plan_id = sp.id 
            WHERE si.schedule_id = ? 
            ORDER BY si.day_of_week, si.time_slot";
    $stmt = mysqli_prepare($conn, $sql);
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
    $sql = "SELECT id, title FROM study_plans WHERE user_id = ? ORDER BY title";
    $stmt = mysqli_prepare($conn, $sql);
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
    // Log đầu vào để debug
    error_log("createSchedule được gọi với: userId=$userId, scheduleName=$scheduleName, startDate=" . ($startDate ?? 'null') . ", endDate=" . ($endDate ?? 'null') . ", isActive=$isActive");
    
    $conn = getDbConnection();
    
    // Kiểm tra kết nối
    if (!$conn) {
        error_log("Không thể kết nối đến database");
        return false;
    }
    
    // Kiểm tra xem user_id có tồn tại không
    $checkUserSql = "SELECT id FROM users WHERE id = ?";
    $checkUserStmt = mysqli_prepare($conn, $checkUserSql);
    mysqli_stmt_bind_param($checkUserStmt, "i", $userId);
    mysqli_stmt_execute($checkUserStmt);
    $userResult = mysqli_stmt_get_result($checkUserStmt);
    
    if (mysqli_num_rows($userResult) == 0) {
        error_log("User ID $userId không tồn tại trong bảng users");
        mysqli_stmt_close($checkUserStmt);
        mysqli_close($conn);
        return false;
    }
    
    mysqli_stmt_close($checkUserStmt);
    
    $sql = "INSERT INTO schedule (user_id, schedule_name, start_date, end_date, is_active) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    
    // Kiểm tra prepared statement
    if (!$stmt) {
        error_log("Lỗi prepare statement: " . mysqli_error($conn));
        mysqli_close($conn);
        return false;
    }
    
    // Xử lý trường hợp startDate và endDate là chuỗi rỗng
    if ($startDate === '') $startDate = null;
    if ($endDate === '') $endDate = null;
    
    // Log các giá trị bind
    error_log("Binding parameters: userId=$userId, scheduleName=$scheduleName, startDate=" . ($startDate ?? 'null') . ", endDate=" . ($endDate ?? 'null') . ", isActive=$isActive");
    
    mysqli_stmt_bind_param($stmt, "isssi", $userId, $scheduleName, $startDate, $endDate, $isActive);
    $result = mysqli_stmt_execute($stmt);
    
    // Log kết quả execute
    error_log("Kết quả execute: " . ($result ? "true" : "false"));
    
    if ($result) {
        $scheduleId = mysqli_insert_id($conn);
        error_log("Thời khóa biểu được tạo với ID: $scheduleId");
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $scheduleId;
    }
    
    // In ra lỗi để debug
    $error = mysqli_error($conn);
    error_log("Lỗi tạo thời khóa biểu: " . $error);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    return false;
}

/**
 * Cập nhật thông tin thời khóa biểu
 */
function updateSchedule($scheduleId, $userId, $scheduleName, $startDate = null, $endDate = null, $isActive = 1) {
    $conn = getDbConnection();
    
    // Kiểm tra kết nối
    if (!$conn) {
        error_log("Không thể kết nối đến database");
        return false;
    }
    
    // Kiểm tra xem user_id có tồn tại không
    $checkUserSql = "SELECT id FROM users WHERE id = ?";
    $checkUserStmt = mysqli_prepare($conn, $checkUserSql);
    mysqli_stmt_bind_param($checkUserStmt, "i", $userId);
    mysqli_stmt_execute($checkUserStmt);
    $userResult = mysqli_stmt_get_result($checkUserStmt);
    
    if (mysqli_num_rows($userResult) == 0) {
        error_log("User ID $userId không tồn tại trong bảng users");
        mysqli_stmt_close($checkUserStmt);
        mysqli_close($conn);
        return false;
    }
    
    mysqli_stmt_close($checkUserStmt);
    
    $sql = "UPDATE schedule SET schedule_name = ?, start_date = ?, end_date = ?, is_active = ? WHERE id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    // Kiểm tra prepared statement
    if (!$stmt) {
        error_log("Lỗi prepare statement: " . mysqli_error($conn));
        mysqli_close($conn);
        return false;
    }
    
    // Xử lý trường hợp startDate và endDate là chuỗi rỗng
    if ($startDate === '') $startDate = null;
    if ($endDate === '') $endDate = null;
    
    mysqli_stmt_bind_param($stmt, "sssiii", $scheduleName, $startDate, $endDate, $isActive, $scheduleId, $userId);
    $result = mysqli_stmt_execute($stmt);
    
    // Kiểm tra số hàng đã cập nhật
    $affectedRows = mysqli_stmt_affected_rows($stmt);
    
    // In ra lỗi để debug
    if (!$result) {
        $error = mysqli_error($conn);
        error_log("Lỗi cập nhật thời khóa biểu: " . $error);
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    // Trả về true nếu có ít nhất một hàng được cập nhật
    return $result && $affectedRows >= 0;
}

/**
 * Thêm môn học vào thời khóa biểu
 */
function addScheduleItem($scheduleId, $studyPlanId, $dayOfWeek, $timeSlot) {
    $conn = getDbConnection();
    $sql = "INSERT INTO schedule_items (schedule_id, study_plan_id, day_of_week, time_slot) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iiss", $scheduleId, $studyPlanId, $dayOfWeek, $timeSlot);
    $result = mysqli_stmt_execute($stmt);
    
    // In ra lỗi để debug
    if (!$result) {
        $error = mysqli_error($conn);
        error_log("Lỗi thêm môn học vào thời khóa biểu: " . $error);
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $result;
}

/**
 * Xóa tất cả môn học trong thời khóa biểu
 */
function deleteAllScheduleItems($scheduleId, $userId) {
    $conn = getDbConnection();
    $sql = "DELETE si FROM schedule_items si 
            JOIN schedule s ON si.schedule_id = s.id 
            WHERE s.id = ? AND s.user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $scheduleId, $userId);
    $result = mysqli_stmt_execute($stmt);
    
    // In ra lỗi để debug
    if (!$result) {
        $error = mysqli_error($conn);
        error_log("Lỗi xóa tất cả môn học khỏi thời khóa biểu: " . $error);
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $result;
}

/**
 * Xóa thời khóa biểu
 */
function deleteSchedule($scheduleId, $userId) {
    $conn = getDbConnection();
    $sql = "DELETE FROM schedule WHERE id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $scheduleId, $userId);
    $result = mysqli_stmt_execute($stmt);
    
    // In ra lỗi để debug
    if (!$result) {
        $error = mysqli_error($conn);
        error_log("Lỗi xóa thời khóa biểu: " . $error);
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $result;
}

/**
 * Xóa môn học khỏi thời khóa biểu
 */
function deleteScheduleItem($itemId, $userId) {
    $conn = getDbConnection();
    $sql = "DELETE si FROM schedule_items si 
            JOIN schedule s ON si.schedule_id = s.id 
            WHERE si.id = ? AND s.user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $itemId, $userId);
    $result = mysqli_stmt_execute($stmt);
    
    // In ra lỗi để debug
    if (!$result) {
        $error = mysqli_error($conn);
        error_log("Lỗi xóa môn học khỏi thời khóa biểu: " . $error);
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $result;
}