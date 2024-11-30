<?php
session_start();
include 'includes/db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Retrieve orders and their food items
$result = $conn->query("SELECT orders.order_id, orders.order_status, orders.order_date, orders.order_type, menu_items.name, order_details.quantity 
                        FROM orders 
                        JOIN order_details ON orders.order_id = order_details.order_id
                        JOIN menu_items ON order_details.item_id = menu_items.item_id
                        WHERE orders.user_id = $user_id
                        ORDER BY orders.order_id DESC");

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[$row['order_id']]['order_id'] = $row['order_id'];
    $orders[$row['order_id']]['order_status'] = $row['order_status'];
    $orders[$row['order_id']]['order_date'] = $row['order_date'];
    $orders[$row['order_id']]['order_type'] = $row['order_type'];
    $orders[$row['order_id']]['items'][] = [
        'name' => $row['name'],
        'quantity' => $row['quantity']
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="css/styles.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Orders</title>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    <div class="main-content">
        <h1>Your Orders</h1>
        <?php if (!empty($orders)): ?>
            <?php foreach ($orders as $order): ?>
                <div class="order">
                    <p><strong>Order ID:</strong> <?php echo htmlspecialchars($order['order_id']); ?></p>
                    <p><strong>Order Type:</strong> <?php echo htmlspecialchars($order['order_type']); ?></p>
                    <p><strong>Status:</strong> <?php echo htmlspecialchars($order['order_status']); ?></p>
                    <p><strong>Order Date:</strong> <?php echo htmlspecialchars($order['order_date']); ?></p>
                    <p><strong>Items:</strong></p>
                    <ul>
                        <?php foreach ($order['items'] as $item): ?>
                            <li><?php echo htmlspecialchars($item['name']) . " (Quantity: " . htmlspecialchars($item['quantity']) . ")"; ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php if ($order['order_status'] === 'Delivering'): ?>
                        <p><a href="track_order.php?order_id=<?php echo $order['order_id']; ?>" class="track-button">Track Order</a></p>
                    <?php endif; ?>
                </div>
                <hr>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-orders-message">
                <p>You have no orders.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

<style>
    .orders-container {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    .order {
        background-color: #f9f9f9;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
    .main-content {
        margin-left: 270px; /* Adjust based on sidebar width */
        padding: 20px;
    }
    .track-button {
        display: inline-block;
        padding: 8px 12px;
        background-color: #28a745;
        color: #fff;
        text-decoration: none;
        border-radius: 5px;
        transition: background-color 0.3s;
    }
    .track-button:hover {
        background-color: #218838;
    }
    .no-orders-message {
        text-align: center;
        margin-top: 50px;
        padding: 20px;
        font-size: 1.2em;
        background-color: #e9ecef;
        border-radius: 8px;
        color: #6c757d;
        font-weight: bold;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
</style>
