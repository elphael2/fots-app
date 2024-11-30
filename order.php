<?php
session_start();
include 'includes/db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="css/styles.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Food</title>
    <style>
        .main-content {
            margin-left: 270px; /* Adjust to ensure content is not hidden by the sidebar */
            padding: 20px;
        }
        .menu {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        .menu-item {
            background-color: #f9f9f9;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        .menu-item:hover {
            transform: translateY(-5px);
        }
        button {
            margin-top: 10px;
            padding: 8px 15px;
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
        <h1>Choose Your Food</h1>
        <div class="menu">
            <?php
            // Query the database for available menu items
            $result = $conn->query("SELECT * FROM menu_items WHERE available = 1");

            // Check if any items were found
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $imagePath = 'images/' . $row['item_id'] . '.jpg'; // Image path based on item_id

                    // Check if the image file exists, use default image if not
                    if (!file_exists($imagePath)) {
                        $imagePath = 'images/default.jpg';
                    }

                    echo "<div class='menu-item'>";
                    echo "<img src='" . htmlspecialchars($imagePath) . "' alt='" . htmlspecialchars($row['name']) . "' style='width:100%; height:auto; border-radius:8px; margin-bottom:10px;'>";
                    echo "<h3>" . htmlspecialchars($row['name']) . " - $" . number_format($row['price'], 2) . "</h3>";
                    echo "<label for='quantity_" . $row['item_id'] . "'>Quantity:</label>";
                    echo "<input type='number' id='quantity_" . $row['item_id'] . "' min='1' value='1'><br>";

                    // Check if the user is logged in
                    if (isset($_SESSION['user_id'])) {
                        echo "<button type='button' onclick=\"addToCart(" . $row['item_id'] . ")\">Add to Cart</button>";
                    } else {
                        echo "<p><a href='login.php'>Log in</a> to add to cart.</p>";
                    }

                    echo "</div>";
                }
            } else {
                echo "<p>No menu items available at the moment.</p>";
            }
            ?>
        </div>
    </div>

    <script>
        function addToCart(itemId) {
            const quantityInput = document.getElementById('quantity_' + itemId);
            const quantity = quantityInput ? quantityInput.value : 1;

            const formData = new FormData();
            formData.append('item_id', itemId);
            formData.append('quantity', quantity);

            fetch('update_cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                alert(data);
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    </script>
</body>
</html>
