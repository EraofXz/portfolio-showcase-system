<?php
require_once 'config/db.php';

// redirect user if session is already active
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: dashboard.php");
    }
    exit();
}
$error = "";
// process login request
if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } else {
        // use prepared statement to prevent sql injection
        $sql = "SELECT id, full_name, email, password, role FROM users WHERE email = ? LIMIT 1";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // check if user exists
        if ($row = mysqli_fetch_assoc($result)) {
            // verify hashed password
            if (password_verify($password, $row['password'])) {
                // store user info inside session
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['full_name'] = $row['full_name'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['role'] = $row['role'];

                // redirect based on user role
                if ($row['role'] == 'admin') {
                    header("Location: admin_dashboard.php");
                } else {
                    header("Location: dashboard.php");
                }
                exit();
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "Email address not found.";
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
    <title>Login - Portfolio Showcase</title>
    <!-- bootstrap 5 and icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .navbar-custom { background-color: #111; }
        .card-custom { max-width: 420px; margin: 50px auto; border-radius: 8px; }
    </style>
</head>
<body>

    <!-- top navbar -->
    <nav class="navbar navbar-dark navbar-custom px-4">
        <a class="navbar-brand fw-bold" href="#">
            <i class="bi bi-mortarboard-fill text-warning"></i> Portfolio Showcase
        </a>
        <div class="d-flex">
            <a href="login.php" class="btn text-white fw-bold"><i class="bi bi-box-arrow-in-right"></i> Login</a>
            <a href="register.php" class="btn text-white"><i class="bi bi-person-plus"></i> Register</a>
        </div>
    </nav>

    <!-- login form box -->
    <div class="container">
        <div class="card card-custom p-4 shadow-sm bg-white">
            <h4 class="text-center text-primary fw-bold mb-4">
                <i class="bi bi-box-arrow-in-right"></i> Login
            </h4>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger py-2"><?php echo $error; ?></div>
            <?php endif; ?>

            <form action="login.php" method="POST" id="loginForm" onsubmit="return validateLogin()">
                <div class="mb-3">
                    <label class="form-label">Email Address:</label>
                    <input type="email" name="email" id="email" class="form-control">
                </div>

                <div class="mb-4">
                    <label class="form-label">Password:</label>
                    <input type="password" name="password" id="password" class="form-control">
                </div>

                <button type="submit" name="login" class="btn btn-primary w-100 py-2">
                    <i class="bi bi-box-arrow-in-right"></i> Log In
                </button>

                <div class="text-center mt-3">
                    <small>Don't have an account? <a href="register.php">Register here</a></small>
                </div>
            </form>
        </div>
    </div>

    <!-- page footer -->
    <div class="text-center text-muted py-3 small">
         © 2026 Politeknik Malaysia - DFP40443 Full Stack Web Development: Mini Project 2
    </div>
    <!-- client side input check -->
    <script>
    function validateLogin() {
        let email = document.getElementById("email").value.trim();
        let pass = document.getElementById("password").value.trim();
        if (email === "" || pass === "") {
            alert("Please fill in both email and password.");
            return false;
        }
        return true;
    }
    </script>
</body>
</html>