<?php
session_start();
include 'includes/db.php';

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = $_POST['order_id'];
    $order_status = $_POST['order_status'];
    $order_type = $_POST['order_type'];

    $stmt = $conn->prepare("UPDATE orders SET order_status = ?, order_type = ? WHERE order_id = ?");
    $stmt->bind_param("ssi", $order_status, $order_type, $order_id);

    if ($stmt->execute()) {
        echo "Order updated successfully.";
    } else {
        echo "Error updating order: " . $stmt->error;
    }
    $stmt->close();
}
header("Location: admin_dashboard.php");
?>
