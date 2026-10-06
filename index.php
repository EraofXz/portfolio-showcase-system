<?php
// load database and session settings
require_once 'config/db.php';
// check session and route user to the right page
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: dashboard.php");
    }
} else {
    // redirect guests to login
    header("Location: login.php");
}
exit();
?>