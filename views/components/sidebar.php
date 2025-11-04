<?php
// Sidebar component
?>
<nav class="sidebar">
    <div class="p-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage == 'dashboard.php') ? 'active' : ''; ?>" href="/BaiTapLon/views/dashboard.php">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage == 'plan_list.php' && !isset($_GET['filter'])) ? 'active' : ''; ?>" href="/BaiTapLon/views/study_plans/plan_list.php">
                    <i class="bi bi-journal-bookmark"></i> Tất cả kế hoạch
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo (isset($_GET['filter']) && $_GET['filter'] == 'in_progress') ? 'active' : ''; ?>" href="/BaiTapLon/views/study_plans/plan_list.php?filter=in_progress">
                    <i class="bi bi-play-circle"></i> Kế hoạch đang thực hiện
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo (isset($_GET['filter']) && $_GET['filter'] == 'completed') ? 'active' : ''; ?>" href="/BaiTapLon/views/study_plans/plan_list.php?filter=completed">
                    <i class="bi bi-check-circle"></i> Kế hoạch đã hoàn thành
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo (isset($_GET['filter']) && $_GET['filter'] == 'incomplete') ? 'active' : ''; ?>" href="/BaiTapLon/views/study_plans/plan_list.php?filter=incomplete">
                    <i class="bi bi-clock"></i> Kế hoạch chưa hoàn thành
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage == 'schedule.php' || $currentPage == 'create_schedule.php' || $currentPage == 'view_schedule.php' || $currentPage == 'edit_schedule.php') ? 'active' : ''; ?>" href="/BaiTapLon/views/schedule/schedule.php">
                    <i class="bi bi-calendar-week"></i> Thời khóa biểu
                </a>
            </li>
        </ul>

        <?php if (isset($currentUser['role']) && $currentUser['role'] === 'admin'): ?>
            <hr>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($currentPage == 'admin_dashboard.php') ? 'active' : ''; ?>" href="/BaiTapLon/views/admin/admin_dashboard.php">
                        <i class="bi bi-people"></i> Quản lý người dùng
                    </a>
                </li>
            </ul>
        <?php endif; ?>
    </div>
</nav>