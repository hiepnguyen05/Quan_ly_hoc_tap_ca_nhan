<?php
require_once __DIR__ . '/../../functions/auth.php';
checkLogin(__DIR__ . '/../../index.php');
$currentUser = getCurrentUser();

// Kiểm tra xem người dùng có phải là admin không
if (!isset($currentUser['role']) || $currentUser['role'] !== 'admin') {
    header("Location: ../../dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Quản Trị - Hệ Thống Quản Lý Học Tập</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="../../css/dashboard.css" rel="stylesheet">
    <style>
        .admin-header {
            background-color: var(--white);
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            padding: 1rem 0;
            margin-bottom: 1.5rem;
        }
        
        .stats-card {
            background-color: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .stats-card .icon-container {
            width: 48px;
            height: 48px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }
        
        .stats-card .icon-container i {
            font-size: 1.5rem;
        }
        
        .stats-card h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 0.25rem;
        }
        
        .stats-card h5 {
            font-size: 0.875rem;
            color: var(--gray-600);
            margin-bottom: 0;
        }
        
        /* Stats Card Colors */
        .stats-card-blue .icon-container {
            background-color: var(--blue-100);
        }
        
        .stats-card-blue .icon-container i {
            color: var(--blue-600);
        }
        
        .stats-card-green .icon-container {
            background-color: var(--green-100);
        }
        
        .stats-card-green .icon-container i {
            color: var(--green-600);
        }
        
        .stats-card-orange .icon-container {
            background-color: var(--orange-100);
        }
        
        .stats-card-orange .icon-container i {
            color: var(--orange-600);
        }
        
        .stats-card-purple .icon-container {
            background-color: var(--purple-100);
        }
        
        .stats-card-purple .icon-container i {
            color: var(--purple-600);
        }
        
        .stats-card-warning .icon-container {
            background-color: var(--yellow-100);
        }
        
        .stats-card-warning .icon-container i {
            color: var(--yellow-700);
        }
        
        .stats-card-info .icon-container {
            background-color: var(--blue-100);
        }
        
        .stats-card-info .icon-container i {
            color: var(--blue-600);
        }
        
        .user-table th {
            background-color: var(--gray-50);
            font-weight: 600;
            border-bottom: 2px solid var(--gray-200);
            color: var(--gray-700);
            padding: 1rem 1.5rem;
        }
        
        .user-table td {
            padding: 1rem 1.5rem;
            vertical-align: middle;
            border-color: var(--gray-200);
        }
        
        .user-table tr:hover {
            background-color: var(--gray-50);
        }
        
        .action-buttons .btn {
            margin-right: 5px;
        }
        
        .modal-header {
            background-color: var(--white);
            border-bottom: 1px solid var(--gray-200);
            border-radius: var(--radius-lg) var(--radius-lg) 0 0 !important;
        }
        
        .system-info-item {
            padding: 10px 0;
            border-bottom: 1px solid var(--gray-200);
        }
        
        .system-info-item:last-child {
            border-bottom: none;
        }
        
        .quick-actions .btn {
            margin-bottom: 10px;
        }
        
        .card {
            background-color: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            font-weight: 600;
            border-bottom: 1px solid var(--gray-200);
            border-radius: var(--radius-lg) var(--radius-lg) 0 0 !important;
            background-color: var(--white);
            padding: 1.25rem 1.5rem;
        }
        
        .bg-light {
            background-color: var(--white) !important;
        }
    </style>
</head>

<body>
    <?php 
    $currentPage = basename($_SERVER['PHP_SELF']);
    include '../../views/components/header.php'; 
    ?>
    
    <div class="container-fluid">
        <div class="row">
            <!-- Admin Sidebar -->
            <div class="col-md-3 col-lg-2 d-none d-md-block">
                <?php include 'admin_sidebar.php'; ?>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <!-- Admin Header -->
                <div class="admin-header">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <h1 class="h3 mb-0">Bảng Điều Khiển Quản Trị</h1>
                                <p class="text-muted mb-0">Quản lý hệ thống và người dùng</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Content Area -->
                <?php include $contentPage; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Tự động đóng thông báo sau 5 giây
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>

</html>