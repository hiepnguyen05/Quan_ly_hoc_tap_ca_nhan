<?php
require_once dirname(__DIR__) . '/db_connection.php';

function createSubject($userId, $name) {
    $conn = getDbConnection();
    
    $stmt = mysqli_prepare($conn, "INSERT INTO subjects (user_id, name) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, "is", $userId, $name);
    
    $success = mysqli_stmt_execute($stmt);
    $subjectId = mysqli_insert_id($conn);
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    return $success ? $subjectId : false;
}

function getSubjectsByUserId($userId) {
    $conn = getDbConnection();
    
    $stmt = mysqli_prepare($conn, "SELECT * FROM subjects WHERE user_id = ? ORDER BY created_at DESC");
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    $subjects = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $subjects[] = $row;
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    return $subjects;
}

function getSubjectById($subjectId, $userId) {
    $conn = getDbConnection();
    
    $stmt = mysqli_prepare($conn, "SELECT * FROM subjects WHERE id = ? AND user_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $subjectId, $userId);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    $subject = mysqli_fetch_assoc($result);
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    return $subject;
}

function updateSubject($subjectId, $userId, $name) {
    $conn = getDbConnection();
    
    $stmt = mysqli_prepare($conn, "UPDATE subjects SET name = ? WHERE id = ? AND user_id = ?");
    mysqli_stmt_bind_param($stmt, "sii", $name, $subjectId, $userId);
    
    $success = mysqli_stmt_execute($stmt);
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    return $success;
}

function deleteSubject($subjectId, $userId) {
    $conn = getDbConnection();
    
    // Xóa các bài tập liên quan đến môn học này
    $stmt = mysqli_prepare($conn, "DELETE FROM assignments WHERE subject_id = ? AND user_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $subjectId, $userId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    // Xóa các phiên học liên quan đến môn học này
    $stmt = mysqli_prepare($conn, "DELETE FROM study_sessions WHERE subject_id = ? AND user_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $subjectId, $userId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    // Xóa môn học
    $stmt = mysqli_prepare($conn, "DELETE FROM subjects WHERE id = ? AND user_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $subjectId, $userId);
    
    $success = mysqli_stmt_execute($stmt);
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    return $success;
}
?>