<?php

session_start();
require_once 'includes/db.php';

if (!isset($pdo)) {
    throw new RuntimeException('Database connection is not available.');
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$query = "SELECT p.*, u.full_name, u.email, c.category_name 
          FROM projects p 
          JOIN users u ON p.user_id = u.id 
          JOIN categories c ON p.category_id = c.id 
          ORDER BY p.created_at DESC";
$stmt = $pdo->query($query);
$submissions = $stmt->fetchAll();
$total_projects = count($submissions);
?>

<?php include 'includes/header.php'; ?>

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>All Student Submissions</h2>
        <span class="badge bg-primary fs-6">Total: <?= $total_projects; ?> Projects</span>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0 align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Student Name</th>
                            <th>Project Title</th>
                            <th>Category</th>
                            <th>Tech Stack</th>
                            <th>Submitted File</th>
                            <th>Date Submitted</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($total_projects > 0): ?>
                            <?php foreach ($submissions as $sub): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($sub['full_name']); ?></strong><br>
                                        <small class="text-muted"><?= htmlspecialchars($sub['email']); ?></small>
                                    </td>
                                    <td class="fw-bold"><?= htmlspecialchars($sub['title']); ?></td>
                                    <td><span class="badge bg-secondary"><?= htmlspecialchars($sub['category_name']); ?></span></td>
                                    <td><?= htmlspecialchars($sub['tech_stack']); ?></td>
                                    <td>
                                        <?php if (!empty($sub['file_path']) && file_exists($sub['file_path'])): ?>
                                            <a href="<?= htmlspecialchars($sub['file_path']); ?>" class="btn btn-sm btn-outline-primary" download>
                                                <i class="bi bi-download"></i> File
                                            </a>
                                        <?php else: ?>
                                            <span class="text-danger small">No File</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('d M Y, h:i A', strtotime($sub['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center py-4">No student submissions available.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>