<?php
// DBConn.php
$host = "localhost";  // Typically "localhost"
$user = "root";       // MySQL & XAMPP Default username 
$password = "";       // Leave blank if no password is set
$database = "myClothingStore"; // Name of your database

// Create connection
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
