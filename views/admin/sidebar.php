<?php 
global $base_dir; 
if (!isset($base_dir)) $base_dir = '/Baitaplon/'; 
$admin_page = basename($_SERVER['PHP_SELF']); 
?>
<div class="d-flex flex-column flex-shrink-0 p-3 bg-white sidebar-sticky">
    <a href="<?php echo $base_dir; ?>views/admin/index.php"
        class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-decoration-none border-bottom pb-3">
        <span class="fs-5 fw-bold text-primary-custom">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                class="bi bi-shield-lock-fill me-2" viewBox="0 0 16 16">
                <path fill-rule="evenodd"
                    d="M8 0c-.001 0-.001 0 0 0v1.111a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V.001a1 1 0 0 1 1-1h5.002a1 1 0 0 1 1 1v.001zm-.001 1.111H3v.889h4v-.889z" />
                <path
                    d="M8 2.222V1a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v1.111h6zM7 6.167a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v3.666a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5V6.167z" />
                <path
                    d="M8 1a2.5 2.5 0 0 0-2.5 2.5V4h5v-.5A2.5 2.5 0 0 0 8 1zm2.5 3H1V.5a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 .5.5V4h-2.5zM1 4.5V11c0 2.22 1.5 4.5 5.5 4.5 4 0 5.5-2.28 5.5-4.5V4.5H1z" />
            </svg>
            ADMIN PANEL
        </span>
    </a>
    <ul class="nav nav-pills flex-column mb-auto mt-3">
        <li class="nav-item">
            <a href="<?php echo $base_dir; ?>views/admin/index.php"
                class="nav-link <?php echo ($admin_page == 'index.php') ? 'active' : 'link-dark'; ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-house-door-fill me-2" viewBox="0 0 16 16">
                    <path
                        d="M6.5 14.5v-2.19c0-.52.42-.94.94-.94h2.12c.52 0 .94.42.94.94v2.19h2c.55 0 1-.45 1-1V7.86c0-.4-.15-.79-.42-1.07L11.53 3.6A1.5 1.5 0 0 0 10.4 3H5.6a1.5 1.5 0 0 0-1.13.4L2.42 6.79a1.5 1.5 0 0 0-.42 1.07v5.64c0 .55.45 1 1 1h2z" />
                </svg>
                Tổng quan
            </a>
        </li>
        <li>
            <a href="<?php echo $base_dir; ?>views/admin/user_management.php"
                class="nav-link <?php echo ($admin_page == 'user_management.php') ? 'active' : 'link-dark'; ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-people-fill me-2" viewBox="0 0 16 16">
                    <path d="M7 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H7zm1.5-10a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z" />
                </svg>
                Quản lý Người dùng
            </a>
        </li>
        <li>
            <a href="#" class="nav-link link-dark">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-book-fill me-2" viewBox="0 0 16 16">
                    <path
                        d="M8 1.783C8.13 1.77 9.42.326 11.29 2.01 13.03 3.566 14.5 5.75 14.5 8c0 2.876-2.01 5.03-3.668 6.5C9.3 15.99 8.13 15.77 8 15.765c-.13.005-1.3.225-2.832-1.235C3.51 13.03 1.5 10.876 1.5 8c0-2.25 1.47-4.434 3.21-5.99C6.58.326 7.87 1.77 8 1.783z" />
                </svg>
                Quản lý Môn học
            </a>
        </li>
        <li>
            <a href="#" class="nav-link link-dark">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-bar-chart-fill me-2" viewBox="0 0 16 16">
                    <path
                        d="M1 11.5a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 0 1H1.5a.5.5 0 0 1-.5-.5zM1 7.5a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 0 1H1.5a.5.5 0 0 1-.5-.5zM1 3.5a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 0 1H1.5a.5.5 0 0 1-.5-.5z" />
                </svg>
                Phân tích & Báo cáo
            </a>
        </li>
        <li>
            <a href="#" class="nav-link link-dark">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-gear-fill me-2" viewBox="0 0 16 16">
                    <path
                        d="M9.405 1.042A2.903 2.903 0 0 1 12.378 1H3.622a2.903 2.903 0 0 1 2.973.042 1.5 1.5 0 0 0 2.81 0zM13.75 4a.5.5 0 0 0-.75.75v3.25a.5.5 0 0 0 .75.75h2.5a.5.5 0 0 0 .75-.75V4.75a.5.5 0 0 0-.75-.75h-2.5zM10.4 12.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1z" />
                </svg>
                Cài đặt Hệ thống
            </a>
        </li>
    </ul>
    <hr>
    <div class="dropdown">
        <a href="#" class="d-flex align-items-center link-dark text-decoration-none dropdown-toggle" id="dropdownUser2"
            data-bs-toggle="dropdown" aria-expanded="false">
            <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?> (Admin)</strong>
        </a>
        <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdownUser2">
            <li><a class="dropdown-item" href="<?php echo $base_dir; ?>handle/logout_process.php">Đăng xuất</a></li>
        </ul>
    </div>
</div>