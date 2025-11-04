<?php
// Lấy tất cả người dùng
require_once __DIR__ . '/../../functions/study_plan_functions.php';
$allUsers = getAllUsers();
$totalUsers = count($allUsers);

// Tính toán thống kê bổ sung
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
?>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-4 col-6 mb-4">
        <div class="stats-card stats-card-blue">
            <div class="icon-container">
                <i class="bi bi-people"></i>
            </div>
            <h2><?php echo $totalUsers; ?></h2>
            <h5>Tổng người dùng</h5>
        </div>
    </div>
    <div class="col-md-4 col-6 mb-4">
        <div class="stats-card stats-card-green">
            <div class="icon-container">
                <i class="bi bi-journal-bookmark"></i>
            </div>
            <h2><?php echo $totalPlans; ?></h2>
            <h5>Tổng kế hoạch</h5>
        </div>
    </div>
    <div class="col-md-4 col-6 mb-4">
        <div class="stats-card stats-card-orange">
            <div class="icon-container">
                <i class="bi bi-clock"></i>
            </div>
            <h2><?php echo $incompletePlans; ?></h2>
            <h5>Chưa hoàn thành</h5>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="row">
    <div class="col-lg-8">
        <!-- User Management -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>
                    <i class="bi bi-people"></i> Quản lý người dùng
                </span>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="bi bi-plus-lg"></i> Thêm người dùng
                </button>
            </div>
            <div class="card-body">
                <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i>
                    <?php echo htmlspecialchars($_GET['success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle"></i>
                    <?php echo htmlspecialchars($_GET['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <div class="table-responsive">
                    <table class="table table-hover user-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Họ tên</th>
                                <th>Tên đăng nhập</th>
                                <th>Email</th>
                                <th>Vai trò</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allUsers as $user): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <?php if ($user['role'] === 'admin'): ?>
                                        <span class="badge bg-danger">Quản trị viên</span>
                                    <?php else: ?>
                                        <span class="badge bg-primary">Người dùng</span>
                                    <?php endif; ?>
                                </td>
                                <td class="action-buttons">
                                    <button class="btn btn-sm btn-outline-warning" 
                                            onclick="editUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['full_name']); ?>', '<?php echo htmlspecialchars($user['username']); ?>', '<?php echo htmlspecialchars($user['email']); ?>', '<?php echo $user['role']; ?>')">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <?php if ($user['id'] != $currentUser['id']): ?>
                                    <a href="../../handle/admin_process.php?action=delete_user&id=<?php echo $user['id']; ?>" 
                                       class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('Bạn có chắc chắn muốn xóa người dùng này?\n\nLưu ý: Hành động này không thể hoàn tác!')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sidebar Content -->
    <div class="col-lg-4">
        <!-- System Information -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> Thông tin hệ thống
            </div>
            <div class="card-body">
                <div class="system-info-item">
                    <div class="d-flex justify-content-between">
                        <span><i class="bi bi-people me-2 text-primary"></i> Tổng người dùng:</span>
                        <strong><?php echo $totalUsers; ?></strong>
                    </div>
                </div>
                <div class="system-info-item">
                    <div class="d-flex justify-content-between">
                        <span><i class="bi bi-journal-bookmark me-2 text-success"></i> Tổng kế hoạch:</span>
                        <strong><?php echo $totalPlans; ?></strong>
                    </div>
                </div>
                <div class="system-info-item">
                    <div class="d-flex justify-content-between">
                        <span><i class="bi bi-check-circle me-2 text-info"></i> Kế hoạch hoàn thành:</span>
                        <strong><?php echo $completedPlans; ?></strong>
                    </div>
                </div>
                <div class="system-info-item">
                    <div class="d-flex justify-content-between">
                        <span><i class="bi bi-clock me-2 text-warning"></i> Kế hoạch chưa hoàn thành:</span>
                        <strong><?php echo $incompletePlans; ?></strong>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-lightning"></i> Hành động nhanh
            </div>
            <div class="card-body">
                <div class="d-grid gap-2 quick-actions">
                    <a href="../study_plans/plan_list.php" class="btn btn-outline-primary">
                        <i class="bi bi-journal-bookmark"></i> Xem tất cả kế hoạch
                    </a>
                    <a href="#" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        <i class="bi bi-person-plus"></i> Thêm người dùng
                    </a>
                    <a href="http://localhost/Baitaplon/handle/logout_process.php" class="btn btn-outline-danger">
                        <i class="bi bi-box-arrow-right"></i> Đăng xuất
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="../../handle/admin_process.php" method="POST">
                <input type="hidden" name="action" value="add_user">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Thêm người dùng mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Họ tên</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Tên đăng nhập</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mật khẩu</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Vai trò</label>
                        <select class="form-select" id="role" name="role">
                            <option value="user">Người dùng</option>
                            <option value="admin">Quản trị viên</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Thêm người dùng</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="../../handle/admin_process.php" method="POST">
                <input type="hidden" name="action" value="edit_user">
                <input type="hidden" id="edit_user_id" name="user_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Chỉnh sửa người dùng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_full_name" class="form-label">Họ tên</label>
                        <input type="text" class="form-control" id="edit_full_name" name="full_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_username" class="form-label">Tên đăng nhập</label>
                        <input type="text" class="form-control" id="edit_username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_role" class="form-label">Vai trò</label>
                        <select class="form-select" id="edit_role" name="role">
                            <option value="user">Người dùng</option>
                            <option value="admin">Quản trị viên</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_password" class="form-label">Mật khẩu (để trống nếu không thay đổi)</label>
                        <input type="password" class="form-control" id="edit_password" name="password">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function editUser(id, fullName, username, email, role) {
        document.getElementById('edit_user_id').value = id;
        document.getElementById('edit_full_name').value = fullName;
        document.getElementById('edit_username').value = username;
        document.getElementById('edit_email').value = email;
        document.getElementById('edit_role').value = role;
        document.getElementById('edit_password').value = '';
        
        var editUserModal = new bootstrap.Modal(document.getElementById('editUserModal'));
        editUserModal.show();
    }
</script>