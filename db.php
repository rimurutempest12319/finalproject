<?php
// db.php - MySQL connection

$host = "localhost";
$user = "root";      // XAMPP default
$pass = "";          // XAMPP default has no password
$db   = "portfolio_db";

$conn = new mysqli($host, $user, $pass, $db);
    
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
