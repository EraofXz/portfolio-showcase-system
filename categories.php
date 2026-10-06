<?php

session_start();
require_once __DIR__ . '/includes/db.php';

if (!isset($pdo) || !$pdo instanceof PDO) {
    http_response_code(500);
    die('Database connection error.');
}


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_name = trim($_POST['category_name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    
    if (empty($category_name)) {
        $error = "Category name is required.";
    } else {
        // Insert into database using prepared statements
        $stmt = $pdo->prepare("INSERT INTO categories (category_name, description) VALUES (?, ?)");
        if ($stmt->execute([$category_name, $description])) {
            $success = "Category added successfully!";
        } else {
            $error = "Failed to add category. Please try again.";
        }
    }
}

$stmt = $pdo->query("SELECT * FROM categories ORDER BY created_at DESC");
$categories = $stmt->fetchAll();
?>

<?php include 'includes/header.php'; ?>

<div class="container my-4">
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <div class="row">
        <!-- Create Category Form -->
        <div class="col-md-5 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-folder-plus"></i> Create Category</h5>
                </div>
                <div class="card-body">
                    <form action="categories.php" method="POST" id="categoryForm">
                        <div class="mb-3">
                            <label for="category_name" class="form-label">Category Name</label>
                            <input type="text" class="form-control" id="category_name" name="category_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">+ Add Category</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Existing Categories</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>Category Name</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($categories) > 0): ?>
                                    <?php foreach ($categories as $cat): ?>
                                        <tr>
                                            <td class="fw-bold text-primary"><?= htmlspecialchars($cat['category_name']); ?></td>
                                            <td><?= htmlspecialchars($cat['description']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="2" class="text-center py-3">No categories found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>