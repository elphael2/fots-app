<?php
session_start();
include 'includes/db.php';

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['item_id'])) {
    $item_id = $_POST['item_id'];

    $stmt = $conn->prepare("DELETE FROM menu_items WHERE item_id = ?");
    $stmt->bind_param("i", $item_id);

    if ($stmt->execute()) {
        echo "Food item deleted successfully.";
    } else {
        echo "Error deleting food item: " . $stmt->error;
    }

    $stmt->close();
    header("Location: admin_dashboard.php");
    exit;
}
?>
