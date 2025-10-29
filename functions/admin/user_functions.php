<?php
require_once __DIR__ . '/../db_connection.php';

function getAllUsers()
{
    $conn = getDbConnection();
    $result = mysqli_query($conn, "SELECT id, full_name, email, role, created_at FROM users ORDER BY created_at DESC");

    $users = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
    mysqli_close($conn);
    return $users;
}

function updateUser($id, $fullName, $role, $email)
{
    $conn = getDbConnection();
    $stmt = mysqli_prepare($conn, "UPDATE users SET full_name = ?, role = ?, email = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "sssi", $fullName, $role, $email, $id);

    $success = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $success;
}

function deleteUser($id)
{
    $conn = getDbConnection();
    $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);

    $success = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $success;
}
