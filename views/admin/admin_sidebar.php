<?php
// Admin Sidebar component
?>
<nav class="sidebar">
    <div class="p-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage == 'admin_dashboard.php') ? 'active' : ''; ?>" href="admin_dashboard.php">
                    <i class="bi bi-speedometer2"></i> Bảng điều khiển
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage == 'user_management.php') ? 'active' : ''; ?>" href="user_management.php">
                    <i class="bi bi-people"></i> Quản lý người dùng
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage == 'study_plan_reports.php') ? 'active' : ''; ?>" href="study_plan_reports.php">
                    <i class="bi bi-file-bar-graph"></i> Báo cáo kế hoạch học
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage == 'system_settings.php') ? 'active' : ''; ?>" href="system_settings.php">
                    <i class="bi bi-gear"></i> Cài đặt hệ thống
                </a>
            </li>
        </ul>

        <hr>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="../dashboard.php">
                    <i class="bi bi-arrow-left-circle"></i> Quay lại trang người dùng
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="http://localhost/Baitaplon/handle/logout_process.php">
                    <i class="bi bi-box-arrow-right"></i> Đăng xuất
                </a>
            </li>
        </ul>
    </div>
</nav>