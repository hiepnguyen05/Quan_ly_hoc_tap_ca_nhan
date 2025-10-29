<?php
session_start();
// Đã sửa đường dẫn theo cấu trúc mới: functions/admin/user_functions.php
require_once '../../functions/admin/user_functions.php';

// KIỂM TRA BẢO MẬT NGHIÊM NGẶT: Chặn truy cập nếu không phải Admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../../index.php");
    exit;
}

$action = $_REQUEST['action'] ?? '';
$redirect_url = '../../views/admin/user_management.php'; // URL chuyển hướng sau xử lý
$status = 'error';

if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['user_id'];
    $fullName = $_POST['full_name'];
    $role = $_POST['role'];
    $email = $_POST['email'];

    // Ngăn chặn admin tự hạ cấp hoặc xóa tài khoản của mình
    if ((int)$id === (int)$_SESSION['user_id'] && $role !== 'admin') {
        $status = 'error_self_demote';
    } elseif (updateUser($id, $fullName, $role, $email)) {
        $status = 'updated';
    }
} elseif ($action === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];

    // Ngăn chặn admin tự xóa tài khoản của mình
    if ((int)$id !== (int)$_SESSION['user_id']) {
        if (deleteUser($id)) {
            $status = 'deleted';
        }
    } else {
        $status = 'error_self_delete';
    }
}

header("Location: " . $redirect_url . "?status=" . $status);
exit;
