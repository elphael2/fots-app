<?php
session_start();
include 'includes/db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    echo "<div class='main-wrapper'><div class='message-container'>Your cart is empty. <a href='order.php'>Go back to the menu</a>.</div></div>";
    exit;
}

// Begin transaction
$conn->begin_transaction();
try {
    // Insert a new order
    $order_type = isset($_POST['order_type']) ? $_POST['order_type'] : 'Dine-In';
    $stmt = $conn->prepare("INSERT INTO orders (user_id, order_status, order_date, order_type) VALUES (?, 'Pending', NOW(), ?)");
    if (!$stmt) {
        echo "Statement preparation failed: " . $conn->error;
        exit;
    }
    $stmt->bind_param("is", $user_id, $order_type);
    $stmt->execute();
    $order_id = $stmt->insert_id;
    $stmt->close();

    // Insert order details (without price column)
    $stmt = $conn->prepare("INSERT INTO order_details (order_id, item_id, quantity) VALUES (?, ?, ?)");
    if (!$stmt) {
        echo "Order details statement preparation failed: " . $conn->error;
        exit;
    }

    foreach ($cart as $item_id => $quantity) {
        $stmt->bind_param("iii", $order_id, $item_id, $quantity);
        if (!$stmt->execute()) {
            echo "Order details execution failed: " . $stmt->error;
            exit;
        }
    }
    $stmt->close();

    // Commit transaction
    $conn->commit();

    // Clear the cart
    unset($_SESSION['cart']);

    echo "<div class='main-wrapper'><div class='message-container success'>";
    echo "<h2>Checkout Successful!</h2>";
    echo "<p>Your order has been placed successfully. <a href='view_orders.php'>View your orders</a></p>";
    echo "</div></div>";
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    echo "<div class='main-wrapper'><div class='message-container error'>";
    echo "<h2>Error Processing Checkout</h2>";
    echo "<p>There was an issue processing your order. Please try again later.</p>";
    echo "</div></div>";
    echo "<p>Error details: " . $e->getMessage() . "</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        /* Ensure the sidebar and main content are correctly aligned */
        body {
            display: flex;
            margin: 0;
        }

        .sidebar {
            width: 270px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #333;
            padding: 20px;
            color: #fff;
        }

        .main-wrapper {
            margin-left: 270px; /* Align with sidebar width */
            padding: 40px 600px; /* Extra padding for centering */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            box-sizing: border-box;
        }

        .message-container {
            max-width: 600px;
            padding: 30px;
            text-align: center;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            font-family: Arial, sans-serif;
            color: #333;
        }

        .message-container h2 {
            font-size: 1.8em;
            margin-bottom: 20px;
        }

        .message-container p {
            font-size: 1.1em;
            color: #555;
        }

        .message-container a {
            color: #28a745;
            text-decoration: none;
            font-weight: bold;
        }

        .message-container a:hover {
            text-decoration: underline;
        }

        .message-container.success h2 {
            color: #28a745;
        }

        .message-container.error h2 {
            color: #dc3545;
        }

        .message-container.error p {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    <div class="main-wrapper">
        <!-- PHP-generated messages appear here -->
    </div>
</body>
</html>



