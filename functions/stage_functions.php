<?php
require_once 'db_connection.php';

/**
 * Lấy tất cả các giai đoạn của một kế hoạch học tập
 * @param int $planId ID kế hoạch
 * @return array Danh sách các giai đoạn
 */
function getPlanStages($planId) {
    $conn = getDbConnection();
    
    $sql = "SELECT * FROM plan_stages WHERE plan_id = ? ORDER BY created_at ASC";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $planId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $stages = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $stages[] = $row;
        }
        
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $stages;
    }
    
    mysqli_close($conn);
    return [];
}

/**
 * Tạo giai đoạn mới cho kế hoạch học tập
 * @param int $planId ID kế hoạch
 * @param string $title Tiêu đề giai đoạn
 * @param string $description Mô tả giai đoạn
 * @param string $deadline Ngày deadline (YYYY-MM-DD)
 * @param string $priority Mức độ ưu tiên (low, medium, high)
 * @return int|false ID giai đoạn mới tạo hoặc false nếu thất bại
 */
function createPlanStage($planId, $title, $description, $deadline, $priority) {
    $conn = getDbConnection();
    
    $sql = "INSERT INTO plan_stages (plan_id, title, description, deadline, priority) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "issss", $planId, $title, $description, $deadline, $priority);
        $success = mysqli_stmt_execute($stmt);
        
        if ($success) {
            $stageId = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return $stageId;
        }
    }
    
    if ($stmt) mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return false;
}

/**
 * Cập nhật trạng thái của một giai đoạn
 * @param int $stageId ID giai đoạn
 * @param int $userId ID người dùng (để kiểm tra quyền)
 * @param string $status Trạng thái mới (not_started, in_progress, completed)
 * @return bool True nếu thành công, False nếu thất bại
 */
function updateStageStatus($stageId, $userId, $status) {
    $conn = getDbConnection();
    
    // Kiểm tra xem giai đoạn có thuộc về người dùng hiện tại không
    $sql = "SELECT ps.* FROM plan_stages ps 
            JOIN study_plans sp ON ps.plan_id = sp.id 
            WHERE ps.id = ? AND sp.user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ii", $stageId, $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) == 0) {
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return false;
        }
        
        mysqli_stmt_close($stmt);
    }
    
    // Cập nhật trạng thái
    $sql = "UPDATE plan_stages SET status = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "si", $status, $stageId);
        $success = mysqli_stmt_execute($stmt);
        
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    
    mysqli_close($conn);
    return false;
}

/**
 * Lấy thông tin một giai đoạn theo ID
 * @param int $stageId ID giai đoạn
 * @param int $userId ID người dùng (để kiểm tra quyền)
 * @return array|null Thông tin giai đoạn hoặc null nếu không tìm thấy
 */
function getStageById($stageId, $userId) {
    $conn = getDbConnection();
    
    $sql = "SELECT ps.* FROM plan_stages ps 
            JOIN study_plans sp ON ps.plan_id = sp.id 
            WHERE ps.id = ? AND sp.user_id = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ii", $stageId, $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            $stage = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return $stage;
        }
        
        mysqli_stmt_close($stmt);
    }
    
    mysqli_close($conn);
    return null;
}

/**
 * Cập nhật thông tin giai đoạn
 * @param int $stageId ID giai đoạn
 * @param int $userId ID người dùng (để kiểm tra quyền)
 * @param string $title Tiêu đề giai đoạn
 * @param string $description Mô tả giai đoạn
 * @param string $deadline Ngày deadline (YYYY-MM-DD)
 * @param string $priority Mức độ ưu tiên (low, medium, high)
 * @return bool True nếu thành công, False nếu thất bại
 */
function updateStage($stageId, $userId, $title, $description, $deadline, $priority) {
    $conn = getDbConnection();
    
    // Kiểm tra xem giai đoạn có thuộc về người dùng hiện tại không
    $sql = "SELECT ps.* FROM plan_stages ps 
            JOIN study_plans sp ON ps.plan_id = sp.id 
            WHERE ps.id = ? AND sp.user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ii", $stageId, $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) == 0) {
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return false;
        }
        
        mysqli_stmt_close($stmt);
    }
    
    // Cập nhật thông tin giai đoạn
    $sql = "UPDATE plan_stages SET title = ?, description = ?, deadline = ?, priority = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssssi", $title, $description, $deadline, $priority, $stageId);
        $success = mysqli_stmt_execute($stmt);
        
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    
    mysqli_close($conn);
    return false;
}

/**
 * Xóa giai đoạn
 * @param int $stageId ID giai đoạn
 * @param int $userId ID người dùng (để kiểm tra quyền)
 * @return bool True nếu thành công, False nếu thất bại
 */
function deleteStage($stageId, $userId) {
    $conn = getDbConnection();
    
    // Kiểm tra xem giai đoạn có thuộc về người dùng hiện tại không
    $sql = "SELECT ps.* FROM plan_stages ps 
            JOIN study_plans sp ON ps.plan_id = sp.id 
            WHERE ps.id = ? AND sp.user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ii", $stageId, $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) == 0) {
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return false;
        }
        
        mysqli_stmt_close($stmt);
    }
    
    // Xóa giai đoạn
    $sql = "DELETE FROM plan_stages WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $stageId);
        $success = mysqli_stmt_execute($stmt);
        
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    
    mysqli_close($conn);
    return false;
}
?>