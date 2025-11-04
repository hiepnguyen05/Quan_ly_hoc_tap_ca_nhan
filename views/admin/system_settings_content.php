<!-- Settings Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="bg-white p-4 rounded shadow-sm">
            <h2 class="mb-0">Cài đặt hệ thống</h2>
            <p class="text-muted mb-0">Quản lý cấu hình và tùy chọn hệ thống</p>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="row">
    <div class="col-lg-8">
        <!-- General Settings -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-gear"></i> Cài đặt chung
            </div>
            <div class="card-body">
                <form>
                    <div class="mb-3">
                        <label for="siteName" class="form-label">Tên hệ thống</label>
                        <input type="text" class="form-control" id="siteName" value="StudyHub - Quản lý Kế hoạch Học tập">
                    </div>
                    <div class="mb-3">
                        <label for="siteDescription" class="form-label">Mô tả hệ thống</label>
                        <textarea class="form-control" id="siteDescription" rows="3">Hệ thống quản lý kế hoạch học tập cá nhân giúp bạn tổ chức và theo dõi tiến độ học tập hiệu quả.</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="adminEmail" class="form-label">Email quản trị</label>
                        <input type="email" class="form-control" id="adminEmail" value="admin@studyhub.com">
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="maintenanceMode" checked>
                        <label class="form-check-label" for="maintenanceMode">
                            Chế độ bảo trì
                        </label>
                    </div>
                    <button type="submit" class="btn btn-primary">Lưu cài đặt</button>
                </form>
            </div>
        </div>
        
        <!-- User Registration Settings -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-person-plus"></i> Cài đặt đăng ký người dùng
            </div>
            <div class="card-body">
                <form>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="allowRegistration" checked>
                        <label class="form-check-label" for="allowRegistration">
                            Cho phép đăng ký mới
                        </label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="emailVerification" checked>
                        <label class="form-check-label" for="emailVerification">
                            Yêu cầu xác minh email
                        </label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="adminApproval" checked>
                        <label class="form-check-label" for="adminApproval">
                            Yêu cầu phê duyệt của quản trị viên
                        </label>
                    </div>
                    <div class="mb-3">
                        <label for="defaultRole" class="form-label">Vai trò mặc định</label>
                        <select class="form-select" id="defaultRole">
                            <option value="user" selected>Người dùng</option>
                            <option value="admin">Quản trị viên</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Lưu cài đặt</button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Sidebar Content -->
    <div class="col-lg-4">
        <!-- System Information -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> Thông tin hệ thống
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <strong><i class="bi bi-server"></i> Phiên bản PHP:</strong> <?php echo phpversion(); ?>
                    </li>
                    <li class="mb-2">
                        <strong><i class="bi bi-database"></i> Hệ quản trị CSDL:</strong> MySQL
                    </li>
                    <li class="mb-2">
                        <strong><i class="bi bi-calendar"></i> Ngày cài đặt:</strong> 01/01/2025
                    </li>
                    <li class="mb-2">
                        <strong><i class="bi bi-code-slash"></i> Phiên bản ứng dụng:</strong> 1.0.0
                    </li>
                </ul>
            </div>
        </div>
        
        <!-- Backup & Maintenance -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-tools"></i> Sao lưu & Bảo trì
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary">
                        <i class="bi bi-cloud-download"></i> Sao lưu cơ sở dữ liệu
                    </button>
                    <button class="btn btn-outline-warning">
                        <i class="bi bi-arrow-repeat"></i> Khôi phục từ sao lưu
                    </button>
                    <button class="btn btn-outline-danger">
                        <i class="bi bi-trash"></i> Xóa dữ liệu cũ
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-lightning"></i> Hành động nhanh
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-success">
                        <i class="bi bi-check-circle"></i> Kiểm tra cập nhật
                    </button>
                    <button class="btn btn-outline-info">
                        <i class="bi bi-bug"></i> Kiểm tra lỗi hệ thống
                    </button>
                    <a href="http://localhost/Baitaplon/handle/logout_process.php" class="btn btn-outline-danger">
                        <i class="bi bi-box-arrow-right"></i> Đăng xuất
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>