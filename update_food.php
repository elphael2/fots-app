<?php
session_start();
include 'includes/db.php';

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_id = $_POST['item_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $available = $_POST['available'];

    $stmt = $conn->prepare("UPDATE menu_items SET name = ?, price = ?, available = ? WHERE item_id = ?");
    $stmt->bind_param("sdii", $name, $price, $available, $item_id);

    if ($stmt->execute()) {
        echo "Food item updated successfully.";
    } else {
        echo "Error updating food item: " . $stmt->error;
    }
    $stmt->close();
}
header("Location: admin_dashboard.php");
?>
