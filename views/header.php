<?php
global $base_dir;
if (!isset($base_dir)) $base_dir = '/Baitaplon/';

$current_page = basename($_SERVER['PHP_SELF']);
$is_admin_area = (strpos($_SERVER['REQUEST_URI'], 'views/admin') !== false);
$is_study_area = (strpos($_SERVER['REQUEST_URI'], 'views/study_management') !== false);

if (isset($_SESSION['user_id'])):
?>
    <nav class="navbar navbar-expand-lg navbar-light fixed-top custom-navbar">
        <div class="container-fluid container">
            <a class="navbar-brand text-primary-custom fw-bold fs-4 d-flex align-items-center" href="<?php echo $base_dir; ?>index.php">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                    class="bi bi-mortarboard-fill me-2" viewBox="0 0 16 16">
                    <path
                        d="M8.211 2.047a.5.5 0 0 0-.422 0l-7.5 3.5a.5.5 0 0 0 .025.917l7.5 3a.5.5 0 0 0 .372 0l7.5-3a.5.5 0 0 0 .025-.917l-7.5-3.5Z" />
                    <path
                        d="M4.176 9.032a.5.5 0 0 0-.084.104l-.597 2.09A.5.5 0 0 0 4.6 12.44L7.52 10.4a.5.5 0 0 0-.104-.084zm6.824 0a.5.5 0 0 0-.104.084l-2.8 2.045a.5.5 0 0 0 .424.825l.597-2.09a.5.5 0 0 0-.084-.104z" />
                </svg>
                MyLMS
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center <?php echo ($current_page == 'index.php' && !$is_admin_area && !$is_study_area) ? 'active' : ''; ?>"
                            href="<?php echo $base_dir; ?>index.php">
                            <i class="bi bi-house-door me-2"></i> Trang Chủ
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center <?php echo ($is_study_area && strpos($_SERVER['REQUEST_URI'], 'dashboard.php') !== false) ? 'active' : ''; ?>"
                            href="<?php echo $base_dir; ?>views/study_management/dashboard.php">
                            <i class="bi bi-speedometer2 me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center <?php echo ($is_study_area && strpos($_SERVER['REQUEST_URI'], 'subjects.php') !== false) ? 'active' : ''; ?>"
                            href="<?php echo $base_dir; ?>views/study_management/subjects.php">
                            <i class="bi bi-journal-bookmark me-2"></i> Môn Học
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center <?php echo ($is_study_area && strpos($_SERVER['REQUEST_URI'], 'assignments.php') !== false) ? 'active' : ''; ?>"
                            href="<?php echo $base_dir; ?>views/study_management/assignments.php">
                            <i class="bi bi-list-task me-2"></i> Bài Tập
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center <?php echo ($is_study_area && strpos($_SERVER['REQUEST_URI'], 'study_sessions.php') !== false) ? 'active' : ''; ?>"
                            href="<?php echo $base_dir; ?>views/study_management/study_sessions.php">
                            <i class="bi bi-clock-history me-2"></i> Thời Gian Học
                        </a>
                    </li>

                    <?php if ($_SESSION['user_role'] === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link text-danger d-flex align-items-center <?php echo ($is_admin_area) ? 'active' : ''; ?>"
                                href="<?php echo $base_dir; ?>views/admin/index.php">
                                <i class="bi bi-shield-lock me-2"></i>
                                <span class="fw-bold">Quản Trị</span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>

                <div class="d-flex align-items-center">
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle rounded-pill d-flex align-items-center" type="button"
                            data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-2"></i>
                            <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item d-flex align-items-center" href="#"><i class="bi bi-person me-2"></i> Hồ sơ cá nhân</a></li>
                            <li><a class="dropdown-item d-flex align-items-center" href="#"><i class="bi bi-gear me-2"></i> Cài đặt</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item text-danger d-flex align-items-center"
                                    href="<?php echo $base_dir; ?>handle/logout_process.php"><i class="bi bi-box-arrow-right me-2"></i> Đăng xuất</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>
<?php endif; ?>