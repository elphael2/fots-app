<?php
session_start();
include 'includes/db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to add items to your cart.";
    exit;
}

// Check if item data was received
if (isset($_POST['item_id']) && isset($_POST['quantity'])) {
    $item_id = intval($_POST['item_id']);
    $quantity = intval($_POST['quantity']);

    if ($quantity > 0) {
        $_SESSION['cart'][$item_id] = $quantity; // Add or update item in cart
        echo "Item successfully added to cart.";
    } else {
        echo "Invalid quantity.";
    }
} else {
    echo "Item data not received.";
}
?>
