<?php
session_start();
include 'includes/db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

// Fetch the order details
$stmt = $conn->prepare("SELECT o.order_id, o.order_status, o.latitude, o.longitude, o.order_type, u.name AS staff_name 
                        FROM orders o
                        LEFT JOIN users u ON o.staff_id = u.user_id
                        WHERE o.user_id = ? AND o.order_id = ?");
$stmt->bind_param("ii", $user_id, $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    echo "<div style='text-align: center; margin-top: 20px;'>No order found or access denied. <a href='view_orders.php'>Go back to orders</a></div>";
    exit;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Your Order</title>
    <link rel="stylesheet" href="css/styles.css">
    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            var orderLat = <?php echo $order['latitude'] ?? 0; ?>;
            var orderLng = <?php echo $order['longitude'] ?? 0; ?>;

            if (orderLat && orderLng) {
                // Display map with order location
                var mapElement = document.getElementById('map');
                mapElement.innerHTML = `<iframe width='100%' height='500px' frameborder='0' style='border:0' 
                src='https://www.openstreetmap.org/export/embed.html?bbox=${orderLng - 0.01}%2C${orderLat - 0.01}%2C${orderLng + 0.01}%2C${orderLat + 0.01}&layer=mapnik&marker=${orderLat}%2C${orderLng}' allowfullscreen></iframe>`;
            } else {
                alert("Order location is not available.");
            }
        });
    </script>
</head>
<body>
    <h1>Track Your Order</h1>
    <div style="text-align: center; margin-bottom: 20px;">
        <p><strong>Order ID:</strong> <?php echo htmlspecialchars($order['order_id']); ?></p>
        <p><strong>Status:</strong> <?php echo htmlspecialchars($order['order_status']); ?></p>
        <p><strong>Order Type:</strong> <?php echo htmlspecialchars($order['order_type']); ?></p>
        <p><strong>Handled By:</strong> <?php echo htmlspecialchars($order['staff_name'] ?? 'Not assigned'); ?></p>
    </div>
    <div id="map" style="width: 100%; height: 500px; background-color: #e0e0e0;"></div>
    <p style="text-align: center; margin-top: 20px;"><a href="view_orders.php">Back to Orders</a></p>
</body>
</html>
