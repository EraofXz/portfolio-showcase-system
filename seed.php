<?php
require_once 'config/db.php';

$messages = [];
$adminEmail = 'admin@example.com';
$adminPass = 'admin123';

// 1. Seed Default Categories if missing
$catCountResult = $conn->query("SELECT COUNT(*) AS total FROM categories");
$catCount = $catCountResult ? (int)$catCountResult->fetch_assoc()['total'] : 0;

if ($catCount === 0) {
    $defaultCategories = [
        ['Cybersecurity & Networking', 'Network tools, security audits, and defensive scripts'],
        ['Internet of Things (IoT)', 'Hardware and software integration projects'],
        ['Mobile Application', 'Native and cross-platform mobile apps for Android and iOS'],
        ['Web Application', 'Full stack web systems built with PHP, MySQL, Bootstrap, AJAX']
    ];

    $catStmt = $conn->prepare("INSERT INTO categories (category_name, description) VALUES (?, ?)");
    foreach ($defaultCategories as $cat) {
        $catStmt->bind_param("ss", $cat[0], $cat[1]);
        $catStmt->execute();
    }
    $catStmt->close();
    $messages[] = ['type' => 'success', 'text' => 'Default project categories created successfully.'];
}

// 2. Seed Default Admin User if missing
$checkAdmin = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
$checkAdmin->bind_param("s", $adminEmail);
$checkAdmin->execute();
$adminResult = $checkAdmin->get_result();

if ($adminResult->num_rows === 0) {
    $name = 'System Administrator';
    $hashedPassword = password_hash($adminPass, PASSWORD_DEFAULT);
    $role = 'admin';

    $insertAdmin = $conn->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)");
    $insertAdmin->bind_param("ssss", $name, $adminEmail, $hashedPassword, $role);
    if ($insertAdmin->execute()) {
        $messages[] = [
            'type' => 'success',
            'text' => "Admin user created successfully! Email: <strong>$adminEmail</strong> | Password: <strong>$adminPass</strong>"
        ];
    } else {
        $messages[] = ['type' => 'danger', 'text' => 'Failed to create admin user: ' . $conn->error];
    }
    $insertAdmin->close();
} else {
    $messages[] = [
        'type' => 'info',
        'text' => "Admin user (<strong>$adminEmail</strong>) already exists in database."
    ];
}
$checkAdmin->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Seeder - Portfolio Showcase</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .card-seeder { max-width: 540px; margin: 80px auto; border-radius: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card card-seeder shadow p-4 bg-white">
            <div class="text-center mb-4">
                <i class="bi bi-database-fill-gear text-primary display-4"></i>
                <h3 class="fw-bold mt-2">Database Setup & Seeder</h3>
                <p class="text-muted small">Automatic initial data setup for testers and teammates</p>
            </div>

            <?php foreach ($messages as $msg): ?>
                <div class="alert alert-<?= $msg['type'] ?> py-2 mb-3">
                    <i class="bi <?= $msg['type'] === 'success' ? 'bi-check-circle-fill' : ($msg['type'] === 'info' ? 'bi-info-circle-fill' : 'bi-exclamation-triangle-fill') ?> me-2"></i>
                    <?= $msg['text'] ?>
                </div>
            <?php endforeach; ?>

            <div class="card bg-light border-0 p-3 mb-4">
                <h6 class="fw-bold mb-2"><i class="bi bi-key-fill text-warning"></i> Admin Credentials</h6>
                <div class="small text-muted mb-1"><strong>Email:</strong> <code>admin@example.com</code></div>
                <div class="small text-muted"><strong>Password:</strong> <code>admin123</code></div>
            </div>

            <div class="d-grid gap-2">
                <a href="login.php" class="btn btn-primary py-2 fw-semibold">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Proceed to Login
                </a>
            </div>
        </div>
    </div>
</body>
</html>

