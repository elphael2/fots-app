<?php
session_start();
include 'includes/db.php';

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $available = $_POST['available'];

    $stmt = $conn->prepare("INSERT INTO menu_items (name, price, available) VALUES (?, ?, ?)");
    $stmt->bind_param("sdi", $name, $price, $available);

    if ($stmt->execute()) {
        echo "Food item added successfully.";
    } else {
        echo "Error adding food item: " . $stmt->error;
    }
    $stmt->close();
}
header("Location: admin_dashboard.php");
?>
