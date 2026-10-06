<?php
// load database config to access session
require_once 'config/db.php';
// clear all session variables and destroy session
session_unset();
session_destroy();
// redirect back to login page
header("Location: login.php");
exit();
?>