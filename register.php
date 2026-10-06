<?php
require_once 'config/db.php';

// if user is already logged in, redirect them to dashboard
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: dashboard.php");
    }
    exit();
}

$error = "";
$success = "";

// handle registration form submit
if (isset($_POST['register'])) {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // basic form checks
    if (empty($full_name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // check if email is already taken
        $check_sql = "SELECT id FROM users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $error = "This email is already registered.";
        } else {
            // hash password using default bcrypt
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role = "student";

            // save new student to users table
            $insert_sql = "INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)";
            $insert_stmt = mysqli_prepare($conn, $insert_sql);
            mysqli_stmt_bind_param($insert_stmt, "ssss", $full_name, $email, $hashed_password, $role);

            if (mysqli_stmt_execute($insert_stmt)) {
                $success = "Registration successful! You can now login.";
            } else {
                $error = "Something went wrong. Please try again.";
            }
            mysqli_stmt_close($insert_stmt);
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - Portfolio Showcase</title>
    <!-- bootstrap 5 and icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .navbar-custom { background-color: #111; }
        .card-custom { max-width: 480px; margin: 40px auto; border-radius: 8px; }
    </style>
</head>
<body>

    <!-- top navbar -->
    <nav class="navbar navbar-dark navbar-custom px-4">
        <a class="navbar-brand fw-bold" href="#">
            <i class="bi bi-mortarboard-fill text-warning"></i> Portfolio Showcase
        </a>
        <div class="d-flex">
            <a href="login.php" class="btn text-white"><i class="bi bi-box-arrow-in-right"></i> Login</a>
            <a href="register.php" class="btn text-white fw-bold"><i class="bi bi-person-plus"></i> Register</a>
        </div>
    </nav>

    <!-- register container -->
    <div class="container">
        <div class="card card-custom p-4 shadow-sm bg-white">
            <h4 class="text-center text-primary fw-bold mb-4">
                <i class="bi bi-person-plus-fill"></i> Create Account
            </h4>

            <!-- display feedback messages -->
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger py-2"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success py-2"><?php echo $success; ?></div>
            <?php endif; ?>

            <form action="register.php" method="POST" id="regForm" onsubmit="return validateForm()">
                <div class="mb-3">
                    <label class="form-label">Full Name:</label>
                    <input type="text" name="full_name" id="full_name" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Email Address:</label>
                    <input type="email" name="email" id="email" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Password:</label>
                    <input type="password" name="password" id="password" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Confirm Password:</label>
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control">
                </div>

                <button type="submit" name="register" class="btn btn-primary w-100 py-2">
                    <i class="bi bi-person-fill"></i> Register
                </button>

                <div class="text-center mt-3">
                    <small>Already have an account? <a href="login.php">Log in here</a></small>
                </div>
            </form>
        </div>
    </div>

    <!-- page footer -->
    <div class="text-center text-muted py-3 small">
        © 2026 Politeknik Malaysia - DFP40443 Full Stack Web Development: Mini Project 2
    </div>

    <!-- simple js client side validation -->
    <script>
    function validateForm() {
        let name = document.getElementById("full_name").value.trim();
        let email = document.getElementById("email").value.trim();
        let pass = document.getElementById("password").value;
        let cpass = document.getElementById("confirm_password").value;

        if (name === "" || email === "" || pass === "" || cpass === "") {
            alert("Please fill in all fields.");
            return false;
        }
        if (pass !== cpass) {
            alert("Passwords do not match.");
            return false;
        }
        return true;
    }
    </script>
</body>
</html>