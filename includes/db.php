<?php
$host = 'localhost'; // The hostname (default for XAMPP is 'localhost')
$db = 'food_ordering_db'; // Replace this with your database name (you'll create this database later)
$user = 'root'; // Default MySQL username for XAMPP
$pass = ''; // Default MySQL password for XAMPP (usually empty)

// Create a connection to MySQL
$conn = new mysqli($host, $user, $pass, $db);

// Check if the connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
