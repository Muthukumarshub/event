<?php
$host = "localhost"; // Change if using cloud DB
$username = "root";  // Your MySQL username
$password = "";      // Your MySQL password
$database = "ems";   // Database name

$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>
