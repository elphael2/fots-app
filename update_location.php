<?php
session_start();
include 'includes/db.php';

// Check if the user is logged in and has staff privileges
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['latitude']) && isset($_POST['longitude']) && isset($_POST['order_id'])) {
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $order_id = $_POST['order_id'];
    $staff_id = $_SESSION['user_id'];

    // Update the current location of the order
    $stmt = $conn->prepare("UPDATE orders SET latitude = ?, longitude = ? WHERE order_id = ? AND staff_id = ?");
    $stmt->bind_param("ddii", $latitude, $longitude, $order_id, $staff_id);

    if ($stmt->execute()) {
        echo "Location updated successfully";
    } else {
        echo "Error updating location: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
