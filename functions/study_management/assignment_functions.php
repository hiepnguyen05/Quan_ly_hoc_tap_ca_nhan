<?php
require_once dirname(__DIR__) . '/db_connection.php';

function createAssignment($userId, $subjectId, $title, $description, $dueDate) {
    $conn = getDbConnection();
    
    $stmt = mysqli_prepare($conn, "INSERT INTO assignments (user_id, subject_id, title, description, due_date) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "iisss", $userId, $subjectId, $title, $description, $dueDate);
    
    $success = mysqli_stmt_execute($stmt);
    $assignmentId = mysqli_insert_id($conn);
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    return $success ? $assignmentId : false;
}

function getAssignmentsByUserId($userId) {
    $conn = getDbConnection();
    
    $stmt = mysqli_prepare($conn, "SELECT a.*, s.name as subject_name FROM assignments a JOIN subjects s ON a.subject_id = s.id WHERE a.user_id = ? ORDER BY a.due_date ASC, a.created_at DESC");
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    $assignments = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $assignments[] = $row;
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    return $assignments;
}

function getAssignmentsBySubjectId($subjectId, $userId) {
    $conn = getDbConnection();
    
    $stmt = mysqli_prepare($conn, "SELECT * FROM assignments WHERE subject_id = ? AND user_id = ? ORDER BY due_date ASC, created_at DESC");
    mysqli_stmt_bind_param($stmt, "ii", $subjectId, $userId);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    $assignments = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $assignments[] = $row;
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    return $assignments;
}

function getAssignmentById($assignmentId, $userId) {
    $conn = getDbConnection();
    
    $stmt = mysqli_prepare($conn, "SELECT a.*, s.name as subject_name FROM assignments a JOIN subjects s ON a.subject_id = s.id WHERE a.id = ? AND a.user_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $assignmentId, $userId);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    $assignment = mysqli_fetch_assoc($result);
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    return $assignment;
}

function updateAssignment($assignmentId, $userId, $title, $description, $dueDate) {
    $conn = getDbConnection();
    
    $stmt = mysqli_prepare($conn, "UPDATE assignments SET title = ?, description = ?, due_date = ? WHERE id = ? AND user_id = ?");
    mysqli_stmt_bind_param($stmt, "sssii", $title, $description, $dueDate, $assignmentId, $userId);
    
    $success = mysqli_stmt_execute($stmt);
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    return $success;
}

function updateAssignmentStatus($assignmentId, $userId, $status) {
    $conn = getDbConnection();
    
    $stmt = mysqli_prepare($conn, "UPDATE assignments SET status = ? WHERE id = ? AND user_id = ?");
    mysqli_stmt_bind_param($stmt, "sii", $status, $assignmentId, $userId);
    
    $success = mysqli_stmt_execute($stmt);
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    return $success;
}

function deleteAssignment($assignmentId, $userId) {
    $conn = getDbConnection();
    
    $stmt = mysqli_prepare($conn, "DELETE FROM assignments WHERE id = ? AND user_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $assignmentId, $userId);
    
    $success = mysqli_stmt_execute($stmt);
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    return $success;
}
?>