<?php
session_start();
include 'includes/db.php';

// Check if the user is logged in and has staff privileges
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];
    $staff_id = $_SESSION['user_id'];

    // Verify the order belongs to the current staff and is in delivering status
    $stmt = $conn->prepare("SELECT order_id FROM orders WHERE order_id = ? AND staff_id = ? AND order_status = 'Delivering'");
    $stmt->bind_param("ii", $order_id, $staff_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();

        // Update the order status to 'Completed'
        $stmt = $conn->prepare("UPDATE orders SET order_status = 'Completed' WHERE order_id = ?");
        $stmt->bind_param("i", $order_id);

        if ($stmt->execute()) {
            echo "<div style='text-align: center; margin-top: 20px;'>Order completed successfully. <a href='staff_dashboard.php'>Back to Dashboard</a></div>";
        } else {
            echo "<div style='text-align: center; margin-top: 20px; color: red;'>Failed to complete the order: " . $stmt->error . "</div>";
        }

        $stmt->close();
    } else {
        echo "<div style='text-align: center; margin-top: 20px; color: red;'>Order not found or not assigned to you.</div>";
    }

    $conn->close();
}
?>
