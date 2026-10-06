<?php

session_start();
$pdo = require __DIR__ . '/includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$stmt_students = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'student'");
$total_students = $stmt_students->fetchColumn();

$stmt_categories = $pdo->query("SELECT COUNT(*) FROM categories");
$total_categories = $stmt_categories->fetchColumn();

$stmt_submissions = $pdo->query("SELECT COUNT(*) FROM projects");
$total_submissions = $stmt_submissions->fetchColumn();

$query = "SELECT p.*, u.full_name, c.category_name 
          FROM projects p 
          JOIN users u ON p.user_id = u.id 
          JOIN categories c ON p.category_id = c.id 
          ORDER BY p.created_at DESC LIMIT 5";
$stmt_recent = $pdo->query($query);
$recent_submissions = $stmt_recent->fetchAll();
?>

<?php include 'includes/header.php'; ?>

<div class="container my-4">
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="mb-4">
        <h2>Admin Overview</h2>
        <p class="text-muted">Lecturer & Coordinator Evaluation Portal</p>
    </div>

    <div class="row text-white mb-5">
        <div class="col-md-4 mb-3">
            <div class="card bg-primary text-white p-3 shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase">Students</h6>
                        <h2 class="display-6 fw-bold"><?= $total_students; ?></h2>
                    </div>
                    <i class="bi bi-people-fill fs-1"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card bg-success text-white p-3 shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase">Categories</h6>
                        <h2 class="display-6 fw-bold"><?= $total_categories; ?></h2>
                    </div>
                    <i class="bi bi-layers-fill fs-1"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card bg-info text-white p-3 shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase">Submissions</h6>
                        <h2 class="display-6 fw-bold"><?= $total_submissions; ?></h2>
                    </div>
                    <i class="bi bi-folder-fill fs-1"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Recent Submissions</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($recent_submissions) > 0): ?>
                            <?php foreach ($recent_submissions as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['full_name']); ?></td>
                                    <td><?= htmlspecialchars($row['title']); ?></td>
                                    <td><span class="badge bg-secondary"><?= htmlspecialchars($row['category_name']); ?></span></td>
                                    <td><?= date('d M Y', strtotime($row['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center py-3">No submissions found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>