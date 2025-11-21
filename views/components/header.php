<?php
// Header component
?>
<header class="shadow-sm">
    <div class="container-fluid h-100">
        <div class="d-flex justify-content-between align-items-center h-100">
            <div class="d-flex align-items-center">
                <button class="menu-toggle d-lg-none">
                    <i class="bi bi-list"></i>
                </button>
                <div class="logo-container me-3">
                    <i class="bi bi-journal-bookmark"></i>
                </div>
                <div>
                    <h1 class="h5 mb-0">StudyHub</h1>
                </div>
            </div>
            
            <div class="d-flex align-items-center">
                <span class="me-3 d-none d-md-block">
                    <?php echo date('d/m/Y'); ?>
                </span>
                
                <div class="dropdown">
                    <button class="btn btn-sm dropdown-toggle d-flex align-items-center" type="button" id="userDropdown" data-bs-toggle="dropdown">
                        <div class="user-avatar me-2">
                            <?php 
                            $username = $currentUser['username'] ?? 'U';
                            echo strtoupper(substr($username, 0, 1));
                            ?>
                        </div>
                        <span class="d-none d-md-inline">
                            <?php echo htmlspecialchars($currentUser['username']); ?>
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><i class="bi bi-person"></i> Hồ sơ</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="/BaiTapLon/handle/logout_process.php"><i class="bi bi-box-arrow-right"></i> Đăng xuất</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>