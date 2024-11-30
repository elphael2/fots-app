<?php
session_start();
include 'includes/db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Successful</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .success-container {
            max-width: 600px;
            margin: 100px auto;
            padding: 40px;
            text-align: center;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            font-family: Arial, sans-serif;
        }
        .success-container h1 {
            color: #28a745;
            margin-bottom: 20px;
        }
        .success-container p {
            font-size: 1.1em;
            color: #555;
            margin-bottom: 20px;
        }
        .success-container a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .success-container a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    <div class="main-content">
        <div class="success-container">
            <h1>Order Placed Successfully!</h1>
            <p>Thank you for your purchase. Your order has been placed and is now being processed.</p>
            <p>You can view your order details in the "View Orders" section.</p>
            <a href="view_orders.php">Go to My Orders</a>
        </div>
    </div>
</body>
</html>
