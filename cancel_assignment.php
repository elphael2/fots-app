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

    // Verify the order belongs to the current staff and is in progress
    $stmt = $conn->prepare("SELECT order_id FROM orders WHERE order_id = ? AND staff_id = ?");
    $stmt->bind_param("ii", $order_id, $staff_id);
    $stmt->execute();
    $stmt->store_result();

    $content = "";

    if ($stmt->num_rows > 0) {
        $stmt->close();
        
        // Update the order to remove the staff assignment and set the status back to 'Pending'
        $stmt = $conn->prepare("UPDATE orders SET staff_id = NULL, order_status = 'Pending' WHERE order_id = ?");
        $stmt->bind_param("i", $order_id);

        if ($stmt->execute()) {
            $content = "<div class='message success'>Order assignment canceled successfully. <a href='staff_dashboard.php'>Back to Dashboard</a></div>";
        } else {
            $content = "<div class='message error'>Failed to cancel the assignment: " . $stmt->error . "</div>";
        }

        $stmt->close();
    } else {
        $content = "<div class='message error'>Order not found or not assigned to you.</div>";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancel Assignment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 80%;
            max-width: 400px;
        }

        h1 {
            font-size: 1.5em;
            margin-bottom: 20px;
            color: #333;
        }

        .message {
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            font-size: 1em;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        a {
            text-decoration: none;
            color: #28a745;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }

    </style>
</head>
<body>
    <div class="container">
        <h1>Cancel Order Assignment</h1>
        <?php echo $content; ?>
    </div>
</body>
</html>

