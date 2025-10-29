<?php
require_once '../functions/auth.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $fullName = $_POST['full_name'];

    if (createUser($email, $password, $fullName)) {
        header("Location: ../index.php?success=registered");
        exit;
    } else {
        header("Location: ../views/register.php?error=email_exists");
        exit;
    }
}
