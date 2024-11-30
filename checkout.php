<?php
session_start();
include 'includes/db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Display the cart items
$cart = $_SESSION['cart'] ?? [];
$totalPrice = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="css/styles.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <style>
        .main-content {
            margin-left: 270px; /* Adjust to ensure content is not hidden by the sidebar */
            padding: 20px;
        }
        .cart-items {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .cart-item {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .checkout-summary {
            margin-top: 20px;
            padding: 15px;
            background-color: #f1f1f1;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        button {
            padding: 10px 20px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    <div class="main-content">
        <h1>Checkout</h1>
        <?php
        if (!empty($cart)) {
            echo "<div class='cart-items'>";
            foreach ($cart as $item_id => $quantity) {
                if ($quantity > 0) {
                    $result = $conn->query("SELECT name, price FROM menu_items WHERE item_id = $item_id");
                    if ($result && $result->num_rows > 0) {
                        $item = $result->fetch_assoc();
                        $itemTotal = $item['price'] * $quantity;
                        $totalPrice += $itemTotal;

                        echo "<div class='cart-item'>";
                        echo "<p><strong>" . htmlspecialchars($item['name']) . "</strong></p>";
                        echo "<p>Quantity: " . $quantity . "</p>";
                        echo "<p>Price: $" . number_format($item['price'], 2) . "</p>";
                        echo "<p>Total: $" . number_format($itemTotal, 2) . "</p>";
                        echo "</div>";
                    }
                }
            }
            echo "</div>";

            echo "<div class='checkout-summary'>";
            echo "<h2>Order Summary</h2>";
            echo "<p><strong>Total Price:</strong> $" . number_format($totalPrice, 2) . "</p>";
            echo "<form method='POST' action='process_checkout.php'>";
            echo "<button type='submit'>Confirm and Pay</button>";
            echo "</form>";
            echo "</div>";
        } else {
            echo "<p>Your cart is empty.</p>";
        }
        ?>
    </div>
</body>
</html>
