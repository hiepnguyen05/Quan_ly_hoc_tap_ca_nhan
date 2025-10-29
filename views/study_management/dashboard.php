<?php
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../../functions/study_management/subject_functions.php';
require_once '../../functions/study_management/assignment_functions.php';
require_once '../../functions/study_management/study_session_functions.php';

$userId = $_SESSION['user_id'];
$userName = $_SESSION['user_name'];

// Lấy thống kê
$subjects = getSubjectsByUserId($userId);
$assignments = getAssignmentsByUserId($userId);
$pendingAssignments = array_filter($assignments, function ($assignment) {
    return $assignment['status'] === 'pending';
});
$totalStudyTime = getTotalStudyTimeByUserId($userId);
$studyTimeBySubject = getStudyTimeBySubject($userId);

// Chuyển đổi tổng thời gian học từ phút sang giờ
$totalStudyHours = round($totalStudyTime / 60, 1);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Quản Lý Học Tập</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../css/main.css">
</head>

<body>
    <?php include '../header.php'; ?>

    <div class="container my-5">
        <div class="row">
            <div class="col-12">
                <h1 class="text-primary-custom mb-4">Dashboard</h1>
                <p class="lead">Chào mừng <strong><?php echo htmlspecialchars($userName); ?></strong> trở lại!</p>
            </div>
        </div>

        <!-- Thống kê -->
        <div class="row mt-4">
            <div class="col-md-3 mb-4">
                <div class="card border-start border-primary border-5 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="card-title text-primary-custom">Môn Học</h5>
                                <p class="card-text fs-2 fw-bold"><?php echo count($subjects); ?></p>
                            </div>
                            <div class="fs-1 text-primary">
                                <i class="bi bi-journal-bookmark"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card border-start border-warning border-5 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="card-title text-primary-custom">Bài Tập Chưa Hoàn Thành</h5>
                                <p class="card-text fs-2 fw-bold"><?php echo count($pendingAssignments); ?></p>
                            </div>
                            <div class="fs-1 text-warning">
                                <i class="bi bi-list-task"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card border-start border-success border-5 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="card-title text-primary-custom">Tổng Thời Gian Học</h5>
                                <p class="card-text fs-2 fw-bold"><?php echo $totalStudyHours; ?>h</p>
                            </div>
                            <div class="fs-1 text-success">
                                <i class="bi bi-clock-history"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card border-start border-info border-5 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="card-title text-primary-custom">Bài Tập Đã Hoàn Thành</h5>
                                <p class="card-text fs-2 fw-bold">
                                    <?php echo count($assignments) - count($pendingAssignments); ?></p>
                            </div>
                            <div class="fs-1 text-info">
                                <i class="bi bi-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Danh sách môn học -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="text-primary-custom">Môn Học Của Bạn</h3>
                    <a href="subjects.php" class="btn btn-primary">Quản Lý Môn Học</a>
                </div>

                <?php if (empty($subjects)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i> Bạn chưa có môn học nào. Hãy <a href="subjects.php"
                            class="alert-link">thêm môn học</a> đầu tiên của bạn!
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach (array_slice($subjects, 0, 4) as $subject): ?>
                            <div class="col-md-3 mb-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($subject['name']); ?></h5>
                                        <p class="card-text">
                                            <small class="text-muted">
                                                <i class="bi bi-calendar-plus me-1"></i>
                                                <?php echo date('d/m/Y', strtotime($subject['created_at'])); ?>
                                            </small>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Danh sách bài tập chưa hoàn thành -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="text-primary-custom">Bài Tập Cần Hoàn Thành</h3>
                    <a href="assignments.php" class="btn btn-primary">Quản Lý Bài Tập</a>
                </div>

                <?php if (empty($pendingAssignments)): ?>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle me-2"></i> Bạn không có bài tập nào cần hoàn thành!
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
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($pendingAssignments, 0, 5) as $assignment): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($assignment['title']); ?></td>
                                        <td><?php echo htmlspecialchars($assignment['subject_name']); ?></td>
                                        <td>
                                            <?php if ($assignment['due_date']): ?>
                                                <?php echo date('d/m/Y', strtotime($assignment['due_date'])); ?>
                                            <?php else: ?>
                                                <span class="text-muted">Không có</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning">Chưa hoàn thành</span>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>