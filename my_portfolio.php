<?php
session_start();

// Validate session securely and restrict to Student role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

require_once 'db.php';
include 'header.php';

$user_id = $_SESSION['user_id'];

// Use SQL JOIN to display combined data using prepared statements
$query = "SELECT p.id, p.title, p.tech_stack, p.file_path, p.created_at, c.category_name 
          FROM portfolio_db_projects p
          JOIN portfolio_db_categories c ON p.category_id = c.id
          WHERE p.user_id = ?
          ORDER BY p.created_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-briefcase"></i> My Portfolio Submissions</h2>
        <a href="submit_project.php" class="btn btn-primary"><i class="bi bi-plus"></i> Add Submission</a>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Project Title</th>
                            <th>Category</th>
                            <th>Tech Stack</th>
                            <th>File</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="text-primary fw-bold"><?php echo htmlspecialchars($row['title']); ?></td>
                                    <td><span class="badge bg-secondary"><?php echo htmlspecialchars($row['category_name']); ?></span></td>
                                    <td><?php echo htmlspecialchars($row['tech_stack']); ?></td>
                                    <td>
                                        <!-- Allow students to download their uploaded files -->
                                        <a href="<?php echo htmlspecialchars($row['file_path']); ?>" class="btn btn-sm btn-outline-secondary" download>
                                            <i class="bi bi-download"></i> Download
                                        </a>
                                    </td>
                                    <td><?php echo date("d Oct Y", strtotime($row['created_at'])); ?></td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-warning"><i class="bi bi-pencil-square"></i></a>
                                        <a href="#" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">No portfolio submissions found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php 
$stmt->close();
include 'footer.php'; 
?>