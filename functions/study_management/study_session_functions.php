<?php
require_once dirname(__DIR__) . '/db_connection.php';

function createStudySession($userId, $subjectId, $date, $duration)
{
    $conn = getDbConnection();

    $stmt = mysqli_prepare($conn, "INSERT INTO study_sessions (user_id, subject_id, date, duration) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "iisi", $userId, $subjectId, $date, $duration);

    $success = mysqli_stmt_execute($stmt);
    $sessionId = mysqli_insert_id($conn);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $success ? $sessionId : false;
}

function getStudySessionsByUserId($userId)
{
    $conn = getDbConnection();

    $stmt = mysqli_prepare($conn, "SELECT ss.*, s.name as subject_name FROM study_sessions ss JOIN subjects s ON ss.subject_id = s.id WHERE ss.user_id = ? ORDER BY ss.date DESC, ss.created_at DESC");
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $sessions = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $sessions[] = $row;
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $sessions;
}

function getStudySessionsBySubjectId($subjectId, $userId)
{
    $conn = getDbConnection();

    $stmt = mysqli_prepare($conn, "SELECT ss.*, s.name as subject_name FROM study_sessions ss JOIN subjects s ON ss.subject_id = s.id WHERE ss.subject_id = ? AND ss.user_id = ? ORDER BY ss.date DESC");
    mysqli_stmt_bind_param($stmt, "ii", $subjectId, $userId);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $sessions = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $sessions[] = $row;
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $sessions;
}

function getStudySessionById($sessionId, $userId)
{
    $conn = getDbConnection();

    $stmt = mysqli_prepare($conn, "SELECT ss.*, s.name as subject_name FROM study_sessions ss JOIN subjects s ON ss.subject_id = s.id WHERE ss.id = ? AND ss.user_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $sessionId, $userId);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $session = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $session;
}

function updateStudySession($sessionId, $userId, $date, $duration)
{
    $conn = getDbConnection();

    $stmt = mysqli_prepare($conn, "UPDATE study_sessions SET date = ?, duration = ? WHERE id = ? AND user_id = ?");
    mysqli_stmt_bind_param($stmt, "siii", $date, $duration, $sessionId, $userId);

    $success = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $success;
}

function deleteStudySession($sessionId, $userId)
{
    $conn = getDbConnection();

    $stmt = mysqli_prepare($conn, "DELETE FROM study_sessions WHERE id = ? AND user_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $sessionId, $userId);

    $success = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $success;
}

function getTotalStudyTimeByUserId($userId)
{
    $conn = getDbConnection();

    $stmt = mysqli_prepare($conn, "SELECT SUM(duration) as total_minutes FROM study_sessions WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $row['total_minutes'] ?? 0;
}

function getStudyTimeBySubject($userId)
{
    $conn = getDbConnection();

    $stmt = mysqli_prepare($conn, "SELECT s.name, SUM(ss.duration) as total_minutes FROM study_sessions ss JOIN subjects s ON ss.subject_id = s.id WHERE ss.user_id = ? GROUP BY ss.subject_id, s.name ORDER BY total_minutes DESC");
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $studyTime = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $studyTime[] = $row;
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $studyTime;
}
