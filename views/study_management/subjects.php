<?php
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../../functions/study_management/subject_functions.php';

$userId = $_SESSION['user_id'];
$subjects = getSubjectsByUserId($userId);

// Xử lý thông báo
$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Môn Học - Quản Lý Học Tập</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../css/main.css">
</head>
<body>
    <?php include '../header.php'; ?>
    
    <div class="container my-5">
        <div class="row">
            <div class="col-12">
                <h1 class="text-primary-custom mb-4">Quản Lý Môn Học</h1>
                
                <!-- Thông báo -->
                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <!-- Form thêm/sửa môn học -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Thêm Môn Học Mới</h5>
                    </div>
                    <div class="card-body">
                        <form action="../../handle/study_management/subject_process.php" method="POST">
                            <input type="hidden" name="action" value="create">
                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label for="name" class="form-label">Tên môn học</label>
                                    <input type="text" class="form-control" id="name" name="name" required placeholder="Ví dụ: Lập trình C++, Toán học, Vật lý...">
                                </div>
                                <div class="col-md-4 mb-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-plus-circle me-2"></i>Thêm Môn Học
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Danh sách môn học -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Danh Sách Môn Học</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($subjects)): ?>
                            <div class="text-center py-5">
                                <i class="bi bi-journal-bookmark fs-1 text-muted"></i>
                                <p class="mt-3">Bạn chưa có môn học nào. Hãy thêm môn học đầu tiên của bạn!</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Tên môn học</th>
                                            <th>Ngày tạo</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($subjects as $subject): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($subject['name']); ?></td>
                                                <td><?php echo date('d/m/Y H:i', strtotime($subject['created_at'])); ?></td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="assignments.php?subject_id=<?php echo $subject['id']; ?>" 
                                                           class="btn btn-sm btn-outline-primary" title="Xem bài tập">
                                                            <i class="bi bi-list-task"></i>
                                                        </a>
                                                        <a href="study_sessions.php?subject_id=<?php echo $subject['id']; ?>" 
                                                           class="btn btn-sm btn-outline-success" title="Xem phiên học">
                                                            <i class="bi bi-clock-history"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-outline-warning" 
                                                                onclick="editSubject(<?php echo $subject['id']; ?>, '<?php echo htmlspecialchars($subject['name']); ?>')" 
                                                                title="Sửa">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                                onclick="deleteSubject(<?php echo $subject['id']; ?>)" 
                                                                title="Xóa">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal sửa môn học -->
    <div class="modal fade" id="editSubjectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sửa Môn Học</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="../../handle/study_management/subject_process.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" id="edit_subject_id" name="subject_id">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Tên môn học</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Cập Nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal xóa môn học -->
    <div class="modal fade" id="deleteSubjectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Xóa Môn Học</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn xóa môn học này? Hành động này sẽ xóa tất cả bài tập và phiên học liên quan đến môn học này.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <form action="../../handle/study_management/subject_process.php" method="POST" class="d-inline">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" id="delete_subject_id" name="subject_id">
                        <button type="submit" class="btn btn-danger">Xóa</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editSubject(id, name) {
            document.getElementById('edit_subject_id').value = id;
            document.getElementById('edit_name').value = name;
            var editModal = new bootstrap.Modal(document.getElementById('editSubjectModal'));
            editModal.show();
        }
        
        function deleteSubject(id) {
            document.getElementById('delete_subject_id').value = id;
            var deleteModal = new bootstrap.Modal(document.getElementById('deleteSubjectModal'));
            deleteModal.show();
        }
    </script>
</body>
</html>