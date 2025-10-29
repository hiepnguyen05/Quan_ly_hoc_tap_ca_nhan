<?php
session_start();
require_once '../functions/auth.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $user = findUserByEmail($email);

    if ($user && password_verify($password, $user['password'])) {
        // Lưu THÊM ID và EMAIL vào session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email']; // Lưu email vào session
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_role'] = $user['role'];

        header("Location: ../index.php");
        exit;
    } else {
        header("Location: ../views/login.php?error=invalid_credentials");
        exit;
    }
}
