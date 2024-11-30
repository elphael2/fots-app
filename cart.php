<?php
session_start();
include 'includes/db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Process items added or removed from the cart
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['quantity'])) {
        foreach ($_POST['quantity'] as $item_id => $quantity) {
            if ($quantity > 0) {
                $_SESSION['cart'][$item_id] = $quantity; // Add or update item in cart
            } else {
                unset($_SESSION['cart'][$item_id]); // Remove item if quantity is zero
            }
        }
    }

    if (isset($_POST['remove_item_id'])) {
        $remove_item_id = $_POST['remove_item_id'];
        unset($_SESSION['cart'][$remove_item_id]); // Remove item from cart
    }
}

// Display the cart
$cart = $_SESSION['cart'] ?? [];
$totalPrice = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="css/styles.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <style>
        .main-content {
            margin-left: 270px; /* Adjusted margin to ensure content is not hidden by the sidebar */
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
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .cart-item img {
            width: 100px;
            height: auto;
            border-radius: 8px;
            margin-right: 15px;
        }
        .cart-item p {
            margin: 0;
        }
        .remove-button {
            padding: 5px 10px;
            background-color: #dc3545;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .remove-button:hover {
            background-color: #c82333;
        }
        button {
            margin-top: 20px;
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
        .empty-cart-message {
            text-align: center;
            padding: 40px;
            background-color: #f8f9fa;
            border-radius: 8px;
            font-size: 1.5em;
            color: #6c757d;
            font-weight: bold;
            margin-top: 50px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .checkout-summary {
            text-align: center; /* Center align all elements within checkout-summary */
        }
        #paypal-button-container {
            display: inline-block;
            margin-top: 20px;
            text-align: center; /* Ensure PayPal button is centered */
        }
    </style>
    <script src="https://www.paypal.com/sdk/js?client-id=AUU_X9qV6yzPoI7pPjjLYg16UOPshl_-fsHjXpUSbttx2b6RxI15BlNHVQuOMg8dR9CyrAQwcsZJv3G1&currency=USD"></script>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    <div class="main-content">
        <h1>Your Cart</h1>
        <?php
        if (!empty($cart)) {
            echo "<div class='cart-items'>";
            foreach ($cart as $item_id => $quantity) {
                if ($quantity > 0) {
                    // Use an alternative method to determine the image path based on item_id
                    $imagePath = 'images/' . $item_id . '.jpg';

                    // Check if the image file exists, use a default image if not
                    if (!file_exists($imagePath)) {
                        $imagePath = 'images/default.jpg';
                    }

                    $result = $conn->query("SELECT name, price FROM menu_items WHERE item_id = $item_id");
                    if ($result && $result->num_rows > 0) {
                        $item = $result->fetch_assoc();
                        $itemTotal = $item['price'] * $quantity;
                        $totalPrice += $itemTotal;

                        echo "<div class='cart-item'>";
                        echo "<img src='" . htmlspecialchars($imagePath) . "' alt='" . htmlspecialchars($item['name']) . "'>";
                        echo "<div>";
                        echo "<p><strong>" . htmlspecialchars($item['name']) . "</strong></p>";
                        echo "<p>Quantity: " . $quantity . "</p>";
                        echo "<p>Total: $" . number_format($itemTotal, 2) . "</p>";
                        echo "</div>";
                        echo "<form method='POST' style='margin: 0;'>";
                        echo "<input type='hidden' name='remove_item_id' value='" . $item_id . "'>";
                        echo "<button type='submit' class='remove-button'>Remove</button>";
                        echo "</form>";
                        echo "</div>";
                    }
                }
            }
            echo "</div>";

            echo "<div class='checkout-summary'>";
            echo "<h2>Order Summary</h2>";
            echo "<p><strong>Total Price:</strong> $" . number_format($totalPrice, 2) . "</p>";
            echo "<form method='POST' action='process_checkout.php'>";
            echo "<input type='hidden' name='order_type' value='Dine-In'>";
            echo "<button type='submit'>Confirm and Pay (Dine-in)</button>";
            echo "</form>";
            echo "<form method='POST' action='process_checkout.php'>";
            echo "<input type='hidden' name='order_type' value='Take-Away'>";
            echo "<button type='submit'>Confirm and Pay (Take-away)</button>";
            echo "</form>";
            
            // PayPal Checkout Button
            echo "<div id='paypal-button-container'></div>";
            echo "</div>";
        } else {
            echo "<div class='empty-cart-message'>Your cart is empty.</div>";
        }
        ?>
    </div>

    <script>
        // Initialize PayPal button
        paypal.Buttons({
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: '<?php echo number_format($totalPrice, 2); ?>' // Total cart price
                        }
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    // Redirect to complete the checkout process
                    fetch('process_checkout.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'order_type=PayPal Take-Away'
                    }).then(response => {
                        window.location.href = 'success_page.php';
                    });
                });
            }
        }).render('#paypal-button-container'); // Display PayPal button
    </script>
</body>
</html>

