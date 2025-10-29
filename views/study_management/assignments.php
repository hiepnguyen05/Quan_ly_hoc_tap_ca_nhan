<?php
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../../functions/study_management/assignment_functions.php';
require_once '../../functions/study_management/subject_functions.php';

$userId = $_SESSION['user_id'];
$subjects = getSubjectsByUserId($userId);

// Lọc theo môn học nếu có tham số
$subjectId = $_GET['subject_id'] ?? '';
if ($subjectId) {
    $assignments = getAssignmentsBySubjectId($subjectId, $userId);
    $selectedSubject = getSubjectById($subjectId, $userId);
} else {
    $assignments = getAssignmentsByUserId($userId);
    $selectedSubject = null;
}

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
    <title>Quản Lý Bài Tập - Quản Lý Học Tập</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../css/main.css">
</head>
<body>
    <?php include '../header.php'; ?>
    
    <div class="container my-5">
        <div class="row">
            <div class="col-12">
                <h1 class="text-primary-custom mb-4">
                    <?php if ($selectedSubject): ?>
                        Bài Tập - <?php echo htmlspecialchars($selectedSubject['name']); ?>
                    <?php else: ?>
                        Quản Lý Bài Tập
                    <?php endif; ?>
                </h1>
                
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
                
                <!-- Form thêm/sửa bài tập -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Thêm Bài Tập Mới</h5>
                    </div>
                    <div class="card-body">
                        <form action="../../handle/study_management/assignment_process.php" method="POST">
                            <input type="hidden" name="action" value="create">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="subject_id" class="form-label">Môn học</label>
                                    <select class="form-select" id="subject_id" name="subject_id" required>
                                        <option value="">Chọn môn học</option>
                                        <?php foreach ($subjects as $subject): ?>
                                            <option value="<?php echo $subject['id']; ?>" 
                                                <?php echo ($subjectId == $subject['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($subject['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="due_date" class="form-label">Ngày hết hạn (tùy chọn)</label>
                                    <input type="date" class="form-control" id="due_date" name="due_date">
                                </div>
                                <div class="col-md-8 mb-3">
                                    <label for="title" class="form-label">Tiêu đề bài tập</label>
                                    <input type="text" class="form-control" id="title" name="title" required placeholder="Ví dụ: Bài tập chương 1, Dự án cuối kỳ...">
                                </div>
                                <div class="col-md-4 mb-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-plus-circle me-2"></i>Thêm Bài Tập
                                    </button>
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="description" class="form-label">Mô tả (tùy chọn)</label>
                                    <textarea class="form-control" id="description" name="description" rows="2" placeholder="Mô tả chi tiết về bài tập..."></textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Danh sách bài tập -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <?php if ($selectedSubject): ?>
                                Danh Sách Bài Tập - <?php echo htmlspecialchars($selectedSubject['name']); ?>
                            <?php else: ?>
                                Danh Sách Bài Tập
                            <?php endif; ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($subjects)): ?>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i> Bạn cần <a href="subjects.php" class="alert-link">thêm môn học</a> trước khi tạo bài tập.
                            </div>
                        <?php elseif (empty($assignments)): ?>
                            <div class="text-center py-5">
                                <i class="bi bi-list-task fs-1 text-muted"></i>
                                <p class="mt-3">
                                    <?php if ($selectedSubject): ?>
                                        Môn học này chưa có bài tập nào.
                                    <?php else: ?>
                                        Bạn chưa có bài tập nào.
                                    <?php endif; ?>
                                </p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Tiêu đề</th>
                                            <th>Môn học</th>
                                            <th>Ngày hết hạn</th>
                                            <th>Trạng thái</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($assignments as $assignment): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($assignment['title']); ?></td>
                                                <td><?php echo htmlspecialchars($assignment['subject_name'] ?? $assignment['subject_id']); ?></td>
                                                <td>
                                                    <?php if ($assignment['due_date']): ?>
                                                        <?php echo date('d/m/Y', strtotime($assignment['due_date'])); ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">Không có</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($assignment['status'] === 'completed'): ?>
                                                        <span class="badge bg-success">Đã hoàn thành</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning">Chưa hoàn thành</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <?php if ($assignment['status'] === 'pending'): ?>
                                                            <form action="../../handle/study_management/assignment_process.php" method="POST" class="d-inline">
                                                                <input type="hidden" name="action" value="update_status">
                                                                <input type="hidden" name="assignment_id" value="<?php echo $assignment['id']; ?>">
                                                                <input type="hidden" name="status" value="completed">
                                                                <button type="submit" class="btn btn-sm btn-outline-success" title="Đánh dấu hoàn thành">
                                                                    <i class="bi bi-check"></i>
                                                                </button>
                                                            </form>
                                                        <?php else: ?>
                                                            <form action="../../handle/study_management/assignment_process.php" method="POST" class="d-inline">
                                                                <input type="hidden" name="action" value="update_status">
                                                                <input type="hidden" name="assignment_id" value="<?php echo $assignment['id']; ?>">
                                                                <input type="hidden" name="status" value="pending">
                                                                <button type="submit" class="btn btn-sm btn-outline-secondary" title="Đánh dấu chưa hoàn thành">
                                                                    <i class="bi bi-arrow-counterclockwise"></i>
                                                                </button>
                                                            </form>
                                                        <?php endif; ?>
                                                        <button type="button" class="btn btn-sm btn-outline-warning" 
                                                                onclick="editAssignment(<?php echo $assignment['id']; ?>, '<?php echo htmlspecialchars($assignment['title']); ?>', '<?php echo htmlspecialchars($assignment['description']); ?>', '<?php echo $assignment['due_date']; ?>', <?php echo $assignment['subject_id']; ?>)" 
                                                                title="Sửa">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                                onclick="deleteAssignment(<?php echo $assignment['id']; ?>)" 
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
    
    <!-- Modal sửa bài tập -->
    <div class="modal fade" id="editAssignmentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sửa Bài Tập</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="../../handle/study_management/assignment_process.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" id="edit_assignment_id" name="assignment_id">
                        <div class="mb-3">
                            <label for="edit_subject_id" class="form-label">Môn học</label>
                            <select class="form-select" id="edit_subject_id" name="subject_id" required>
                                <?php foreach ($subjects as $subject): ?>
                                    <option value="<?php echo $subject['id']; ?>">
                                        <?php echo htmlspecialchars($subject['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_title" class="form-label">Tiêu đề bài tập</label>
                            <input type="text" class="form-control" id="edit_title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_due_date" class="form-label">Ngày hết hạn (tùy chọn)</label>
                            <input type="date" class="form-control" id="edit_due_date" name="due_date">
                        </div>
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Mô tả (tùy chọn)</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="2"></textarea>
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
    
    <!-- Modal xóa bài tập -->
    <div class="modal fade" id="deleteAssignmentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Xóa Bài Tập</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn xóa bài tập này?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <form action="../../handle/study_management/assignment_process.php" method="POST" class="d-inline">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" id="delete_assignment_id" name="assignment_id">
                        <button type="submit" class="btn btn-danger">Xóa</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editAssignment(id, title, description, dueDate, subjectId) {
            document.getElementById('edit_assignment_id').value = id;
            document.getElementById('edit_title').value = title;
            document.getElementById('edit_description').value = description;
            document.getElementById('edit_due_date').value = dueDate;
            document.getElementById('edit_subject_id').value = subjectId;
            var editModal = new bootstrap.Modal(document.getElementById('editAssignmentModal'));
            editModal.show();
        }
        
        function deleteAssignment(id) {
            document.getElementById('delete_assignment_id').value = id;
            var deleteModal = new bootstrap.Modal(document.getElementById('deleteAssignmentModal'));
            deleteModal.show();
        }
    </script>
</body>
</html>