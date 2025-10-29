<?php
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../../functions/study_management/study_session_functions.php';
require_once '../../functions/study_management/subject_functions.php';

$userId = $_SESSION['user_id'];
$subjects = getSubjectsByUserId($userId);

// Lọc theo môn học nếu có tham số
$subjectId = $_GET['subject_id'] ?? '';
if ($subjectId) {
    $sessions = getStudySessionsBySubjectId($subjectId, $userId);
    $selectedSubject = getSubjectById($subjectId, $userId);
} else {
    $sessions = getStudySessionsByUserId($userId);
    $selectedSubject = null;
}

// Tính tổng thời gian học
$totalStudyTime = 0;
foreach ($sessions as $session) {
    $totalStudyTime += $session['duration'];
}
$totalStudyHours = round($totalStudyTime / 60, 1);

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
    <title>Theo Dõi Thời Gian Học - Quản Lý Học Tập</title>
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
                        Thời Gian Học - <?php echo htmlspecialchars($selectedSubject['name']); ?>
                    <?php else: ?>
                        Theo Dõi Thời Gian Học
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
                
                <!-- Thống kê -->
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">Tổng Số Phiên Học</h5>
                                <p class="card-text fs-2 fw-bold"><?php echo count($sessions); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">Tổng Thời Gian Học</h5>
                                <p class="card-text fs-2 fw-bold"><?php echo $totalStudyHours; ?> giờ</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">Thời Gian Trung Bình</h5>
                                <p class="card-text fs-2 fw-bold">
                                    <?php echo count($sessions) > 0 ? round($totalStudyTime / count($sessions), 0) : 0; ?> phút
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Form thêm/sửa phiên học -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Ghi Lại Phiên Học Mới</h5>
                    </div>
                    <div class="card-body">
                        <form action="../../handle/study_management/study_session_process.php" method="POST">
                            <input type="hidden" name="action" value="create">
                            <div class="row">
                                <div class="col-md-4 mb-3">
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
                                <div class="col-md-4 mb-3">
                                    <label for="date" class="form-label">Ngày học</label>
                                    <input type="date" class="form-control" id="date" name="date" required value="<?php echo date('Y-m-d'); ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="duration" class="form-label">Thời lượng (phút)</label>
                                    <input type="number" class="form-control" id="duration" name="duration" required min="1" placeholder="Ví dụ: 60">
                                </div>
                                <div class="col-12 mb-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-plus-circle me-2"></i>Ghi Lại Phiên Học
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Danh sách phiên học -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <?php if ($selectedSubject): ?>
                                Danh Sách Phiên Học - <?php echo htmlspecialchars($selectedSubject['name']); ?>
                            <?php else: ?>
                                Danh Sách Phiên Học
                            <?php endif; ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($subjects)): ?>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i> Bạn cần <a href="subjects.php" class="alert-link">thêm môn học</a> trước khi ghi lại phiên học.
                            </div>
                        <?php elseif (empty($sessions)): ?>
                            <div class="text-center py-5">
                                <i class="bi bi-clock-history fs-1 text-muted"></i>
                                <p class="mt-3">
                                    <?php if ($selectedSubject): ?>
                                        Môn học này chưa có phiên học nào.
                                    <?php else: ?>
                                        Bạn chưa ghi lại phiên học nào.
                                    <?php endif; ?>
                                </p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Môn học</th>
                                            <th>Ngày học</th>
                                            <th>Thời lượng</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($sessions as $session): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($session['subject_name'] ?? $session['subject_id']); ?></td>
                                                <td><?php echo date('d/m/Y', strtotime($session['date'])); ?></td>
                                                <td><?php echo $session['duration']; ?> phút</td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button type="button" class="btn btn-sm btn-outline-warning" 
                                                                onclick="editSession(<?php echo $session['id']; ?>, <?php echo $session['subject_id']; ?>, '<?php echo $session['date']; ?>', <?php echo $session['duration']; ?>)" 
                                                                title="Sửa">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                                onclick="deleteSession(<?php echo $session['id']; ?>)" 
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
    
    <!-- Modal sửa phiên học -->
    <div class="modal fade" id="editSessionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sửa Phiên Học</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="../../handle/study_management/study_session_process.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" id="edit_session_id" name="session_id">
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
                            <label for="edit_date" class="form-label">Ngày học</label>
                            <input type="date" class="form-control" id="edit_date" name="date" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_duration" class="form-label">Thời lượng (phút)</label>
                            <input type="number" class="form-control" id="edit_duration" name="duration" required min="1">
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
    
    <!-- Modal xóa phiên học -->
    <div class="modal fade" id="deleteSessionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Xóa Phiên Học</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn xóa phiên học này?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <form action="../../handle/study_management/study_session_process.php" method="POST" class="d-inline">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" id="delete_session_id" name="session_id">
                        <button type="submit" class="btn btn-danger">Xóa</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editSession(id, subjectId, date, duration) {
            document.getElementById('edit_session_id').value = id;
            document.getElementById('edit_subject_id').value = subjectId;
            document.getElementById('edit_date').value = date;
            document.getElementById('edit_duration').value = duration;
            var editModal = new bootstrap.Modal(document.getElementById('editSessionModal'));
            editModal.show();
        }
        
        function deleteSession(id) {
            document.getElementById('delete_session_id').value = id;
            var deleteModal = new bootstrap.Modal(document.getElementById('deleteSessionModal'));
            deleteModal.show();
        }
    </script>
</body>
</html>