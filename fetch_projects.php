<?php
require_once 'config/db.php';

// Extract search keywords from AJAX query string
$search = trim($_GET['search'] ?? '');

$sql = "SELECT p.*, u.full_name AS student_name, c.category_name 
        FROM projects p 
        JOIN users u ON p.user_id = u.id 
        LEFT JOIN categories c ON p.category_id = c.id";

if (!empty($search)) {
    $sql .= " WHERE p.title LIKE ? OR p.tech_stack LIKE ? OR u.full_name LIKE ?";
}

$sql .= " ORDER BY p.created_at DESC";

$stmt = $conn->prepare($sql);

if (!empty($search)) {
    $searchTerm = "%{$search}%";
    $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0):
    while ($row = $result->fetch_assoc()):
?>
    <div class="col-md-4 mb-4">
        <div class="card h-100 shadow-sm border-0 project-card">
            <div class="card-body d-flex flex-column">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge bg-primary text-white"><?= htmlspecialchars($row['category_name'] ?? 'General') ?></span>
                    <small class="text-muted"><i class="bi bi-clock"></i> <?= date('d/m/Y', strtotime($row['created_at'])) ?></small>
                </div>
                <h5 class="card-title fw-bold text-primary mb-2"><?= htmlspecialchars($row['title']) ?></h5>
                <p class="card-text text-secondary small flex-grow-1">
                    <?= htmlspecialchars(mb_strimwidth($row['description'], 0, 110, "...")) ?>
                </p>
                <div class="mb-3">
                    <div class="tech-stack-label">Tech Stack:</div>
                    <span class="tech-stack-value text-uppercase"><?= htmlspecialchars($row['tech_stack']) ?></span>
                </div>
                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                    <span class="small text-muted"><i class="bi bi-person"></i> <?= htmlspecialchars($row['student_name']) ?></span>
                    <?php if (!empty($row['file_path'])): ?>
                        <a href="<?= htmlspecialchars($row['file_path']) ?>" class="btn btn-sm btn-outline-primary" download>
                            <i class="bi bi-file-earmark-arrow-down"></i> Report
                        </a>
                    <?php else: ?>
                        <span class="badge bg-light text-muted border">No Report</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php
    endwhile;
else:
?>
    <div class="col-12 text-center py-5 text-muted">
        <i class="bi bi-folder-x fs-1 d-block mb-2"></i>
        No projects found matching your search.
    </div>
<?php
endif;
$stmt->close();
?>