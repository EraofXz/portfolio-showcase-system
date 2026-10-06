<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "assignment_system";

// MySQLi connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>