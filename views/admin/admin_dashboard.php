<?php
require_once __DIR__ . '/../../functions/auth.php';
require_once __DIR__ . '/../../functions/study_plan_functions.php';
checkLogin(__DIR__ . '/../../index.php');
$currentUser = getCurrentUser();

// Kiểm tra xem người dùng có phải là admin không
if (!isset($currentUser['role']) || $currentUser['role'] !== 'admin') {
    header("Location: ../../dashboard.php");
    exit();
}

// Lấy thống kê
$allUsers = getAllUsers();
$totalUsers = count($allUsers);
$totalPlans = 0;
$completedPlans = 0;
$incompletePlans = 0;

foreach ($allUsers as $user) {
    $plans = getUserStudyPlans($user['id']);
    $totalPlans += count($plans);
    
    foreach ($plans as $plan) {
        $progress = calculatePlanProgress($plan['id']);
        if ($progress['percentage'] == 100) {
            $completedPlans++;
        } else {
            $incompletePlans++;
        }
    }
}
$contentPage = 'admin_dashboard_content.php';
include 'admin_layout.php';
?>