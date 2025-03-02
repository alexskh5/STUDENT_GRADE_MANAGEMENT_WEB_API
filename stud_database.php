<?php
// stud_database.php - Database connection
$host = 'localhost';
$user = 'root'; // Default XAMPP username
$pass = '';    // No password for root user
$db = 'student_grade';
$port = 3307; // Add the correct port

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
