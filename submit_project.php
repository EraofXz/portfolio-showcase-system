<?php
session_start();

// Restrict access based on user role (Student only)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

require_once 'db.php';
$message = '';
$messageType = '';

// Handle form submission securely using POST method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $category_id = trim($_POST['category_id']);
    $tech_stack = trim($_POST['tech_stack']);
    $description = trim($_POST['description']);
    $user_id = $_SESSION['user_id'];
    
    // Server-side validation for empty fields
    if (empty($title) || empty($category_id) || empty($tech_stack) || empty($description) || empty($_FILES['file_upload']['name'])) {
        $message = "All fields are required!";
        $messageType = "danger";
    } else {
        // File handling and upload validation
        $uploadDir = 'uploads/';
        $fileName = basename($_FILES['file_upload']['name']);
        $fileSize = $_FILES['file_upload']['size'];
        $fileTmp = $_FILES['file_upload']['tmp_name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        $allowedExts = array('pdf', 'docx', 'zip');
        $maxFileSize = 5 * 1024 * 1024; // Max 5MB
        
        // Validate file type and size
        if (!in_array($fileExt, $allowedExts)) {
            $message = "Invalid file type. Only PDF, DOCX, or ZIP allowed.";
            $messageType = "danger";
        } elseif ($fileSize > $maxFileSize) {
            $message = "File size exceeds the 5MB limit.";
            $messageType = "danger";
        } else {
            // Generate unique filename to avoid overwriting and store path
            $newFileName = uniqid() . '_' . $fileName;
            $uploadFilePath = $uploadDir . $newFileName;
            
            if (move_uploaded_file($fileTmp, $uploadFilePath)) {
                // Prepared statement for secure database INSERT
                $stmt = $conn->prepare("INSERT INTO portfolio_db_projects (user_id, category_id, title, description, tech_stack, file_path) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("iissss", $user_id, $category_id, $title, $description, $tech_stack, $uploadFilePath);
                
                if ($stmt->execute()) {
                    $message = "Project submitted successfully!";
                    $messageType = "success";
                } else {
                    $message = "Database error. Failed to save project details.";
                    $messageType = "danger";
                }
                $stmt->close();
            } else {
                $message = "Error uploading the file to the server folder.";
                $messageType = "danger";
            }
        }
    }
}

// Fetch categories for the select dropdown
$categoriesResult = $conn->query("SELECT id, category_name FROM portfolio_db_categories");

include 'header.php';
?>

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-white">
            <h4><i class="bi bi-cloud-arrow-up"></i> Submit Portfolio Project</h4>
        </div>
        <div class="card-body">
            <!-- Display appropriate error and success messages -->
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form action="submit_project.php" method="POST" enctype="multipart/form-data" id="submitProjectForm">
                <div class="mb-3">
                    <label class="form-label">Project Title</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-select" required>
                        <option value="">-- Select Category --</option>
                        <?php while ($cat = $categoriesResult->fetch_assoc()): ?>
                            <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Tech Stack (Comma Separated)</label>
                    <input type="text" name="tech_stack" class="form-control" placeholder="e.g. PHP, MySQL, Bootstrap 5, AJAX" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Project Description</label>
                    <textarea name="description" class="form-control" rows="4" required></textarea>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Upload Documentation (PDF, DOCX, or ZIP - Max 5MB)</label>
                    <input type="file" name="file_upload" id="file_upload" class="form-control" accept=".pdf,.docx,.zip" required>
                    <div class="invalid-feedback" id="fileError"></div>
                </div>
                
                <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i> Submit Entry</button>
            </form>
        </div>
    </div>
</div>

<!-- Client-side validation using JavaScript -->
<script>
document.getElementById('submitProjectForm').addEventListener('submit', function(e) {
    const fileInput = document.getElementById('file_upload');
    const fileError = document.getElementById('fileError');
    
    if (fileInput.files.length > 0) {
        const fileSize = fileInput.files[0].size;
        const maxSize = 5 * 1024 * 1024; // 5MB limit
        
        if (fileSize > maxSize) {
            e.preventDefault();
            fileInput.classList.add('is-invalid');
            fileError.textContent = 'File size exceeds the 5MB limit. Please upload a smaller file.';
        } else {
            fileInput.classList.remove('is-invalid');
        }
    }
});
</script>

<?php include 'footer.php'; ?>