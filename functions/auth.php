<?php
require_once 'db_connection.php';

function createUser($email, $password, $fullName)
{
    $conn = getDbConnection();

    // Kiểm tra email đã tồn tại chưa
    $check_stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
    mysqli_stmt_bind_param($check_stmt, "s", $email);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);

    if (mysqli_stmt_num_rows($check_stmt) > 0) {
        mysqli_stmt_close($check_stmt);
        mysqli_close($conn);
        return false; // Email đã tồn tại
    }
    mysqli_stmt_close($check_stmt);

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = mysqli_prepare($conn, "INSERT INTO users (email, password, full_name) VALUES (?, ?, ?)");

    // role mặc định là 'user' (đã thiết lập trong SQL)
    mysqli_stmt_bind_param($stmt, "sss", $email, $hashedPassword, $fullName);

    $success = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $success;
}

function findUserByEmail($email)
{
    $conn = getDbConnection();
    $stmt = mysqli_prepare($conn, "SELECT id, full_name, password, role FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $user;
}
