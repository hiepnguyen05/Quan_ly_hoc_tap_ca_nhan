<?php
require_once 'db_connection.php';

/**
 * Lấy tất cả kế hoạch học tập của người dùng
 * @param int $userId ID người dùng
 * @return array Danh sách kế hoạch học tập
 */
function getUserStudyPlans($userId) {
    $conn = getDbConnection();
    
    $sql = "SELECT * FROM study_plans WHERE user_id = ? ORDER BY created_at DESC";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
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
    
    mysqli_close($conn);
    return [];
}

/**
 * Lấy thông tin một kế hoạch học tập theo ID
 * @param int $planId ID kế hoạch
 * @param int $userId ID người dùng (để kiểm tra quyền)
 * @return array|null Thông tin kế hoạch hoặc null nếu không tìm thấy
 */
function getStudyPlanById($planId, $userId) {
    $conn = getDbConnection();
    
    $sql = "SELECT * FROM study_plans WHERE id = ? AND user_id = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ii", $planId, $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            $plan = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return $plan;
        }
        
        mysqli_stmt_close($stmt);
    }
    
    mysqli_close($conn);
    return null;
}

/**
 * Tạo kế hoạch học tập mới
 * @param int $userId ID người dùng
 * @param string $title Tiêu đề kế hoạch
 * @param string $description Mô tả kế hoạch
 * @param string $startDate Ngày bắt đầu (YYYY-MM-DD)
 * @param string $endDate Ngày kết thúc (YYYY-MM-DD)
 * @return int|false ID kế hoạch mới tạo hoặc false nếu thất bại
 */
function createStudyPlan($userId, $title, $description, $startDate, $endDate) {
    $conn = getDbConnection();
    
    $sql = "INSERT INTO study_plans (user_id, title, description, start_date, end_date) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "issss", $userId, $title, $description, $startDate, $endDate);
        $success = mysqli_stmt_execute($stmt);
        
        if ($success) {
            $planId = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return $planId;
        }
    }
    
    if ($stmt) mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return false;
}

/**
 * Cập nhật thông tin kế hoạch học tập
 * @param int $planId ID kế hoạch
 * @param int $userId ID người dùng (để kiểm tra quyền)
 * @param string $title Tiêu đề kế hoạch
 * @param string $description Mô tả kế hoạch
 * @param string $startDate Ngày bắt đầu (YYYY-MM-DD)
 * @param string $endDate Ngày kết thúc (YYYY-MM-DD)
 * @return bool True nếu thành công, False nếu thất bại
 */
function updateStudyPlan($planId, $userId, $title, $description, $startDate, $endDate) {
    $conn = getDbConnection();
    
    $sql = "UPDATE study_plans SET title = ?, description = ?, start_date = ?, end_date = ? WHERE id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssssii", $title, $description, $startDate, $endDate, $planId, $userId);
        $success = mysqli_stmt_execute($stmt);
        
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    
    mysqli_close($conn);
    return false;
}

/**
 * Xóa kế hoạch học tập
 * @param int $planId ID kế hoạch
 * @param int $userId ID người dùng (để kiểm tra quyền)
 * @return bool True nếu thành công, False nếu thất bại
 */
function deleteStudyPlan($planId, $userId) {
    $conn = getDbConnection();
    
    $sql = "DELETE FROM study_plans WHERE id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ii", $planId, $userId);
        $success = mysqli_stmt_execute($stmt);
        
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    
    mysqli_close($conn);
    return false;
}

/**
 * Tính tiến độ của kế hoạch học tập
 * @param int $planId ID kế hoạch
 * @return array ['total' => tổng số giai đoạn, 'completed' => số giai đoạn đã hoàn thành, 'percentage' => phần trăm hoàn thành]
 */
function calculatePlanProgress($planId) {
    $conn = getDbConnection();
    
    $sql = "SELECT COUNT(*) as total, SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed FROM plan_stages WHERE plan_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $planId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            $total = (int)$row['total'];
            $completed = (int)$row['completed'];
            $percentage = ($total > 0) ? round(($completed / $total) * 100) : 0;
            
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return [
                'total' => $total,
                'completed' => $completed,
                'percentage' => $percentage
            ];
        }
        
        mysqli_stmt_close($stmt);
    }
    
    mysqli_close($conn);
    return ['total' => 0, 'completed' => 0, 'percentage' => 0];
}
?>