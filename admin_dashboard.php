<?php
session_start();
include 'includes/db.php';

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Fetch all food items, orders, and users
$food_items = $conn->query("SELECT * FROM menu_items");
$orders = $conn->query("SELECT * FROM orders");
$users = $conn->query("SELECT * FROM users");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
        }
        .logout-button {
            position: fixed;
            top: 20px;
            left: 20px;
            background-color: #dc3545;
            color: #fff;
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            font-size: 14px;
            text-decoration: none;
            text-align: center;
            display: inline-block;
        }
        .logout-button:hover {
            background-color: #c82333;
        }
        .main-content {
            max-width: 1200px;
            margin: 60px auto; /* Adjusted margin to avoid overlap with logout button */
            padding: 20px;
        }
        h1 {
            color: #007bff;
            text-align: center;
            margin-bottom: 20px;
        }
        h2 {
            color: #555;
            margin-top: 40px;
        }
        .admin-section {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        .admin-section h3 {
            margin-top: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #007bff;
            color: #fff;
        }
        input[type="text"], input[type="number"], input[type="email"], select {
            width: 90%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            padding: 8px 12px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 10px;
        }
        button:hover {
            background-color: #218838;
        }
        .action-buttons {
            display: flex;
            gap: 5px;
            justify-content: center;
        }
        .delete-button {
            background-color: #dc3545;
        }
        .delete-button:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <!-- Logout Button -->
    <a href="logout.php" class="logout-button">Logout</a>

    <div class="main-content">
        <h1>Admin Panel</h1>

        <!-- Manage Food Items Section -->
        <h2>Manage Food Items</h2>
        <div class="admin-section">
            <form method="POST" action="add_food.php">
                <h3>Add New Food Item</h3>
                <label for="name">Name:</label>
                <input type="text" name="name" required><br>
                <label for="price">Price:</label>
                <input type="number" step="0.01" name="price" required><br>
                <label for="available">Available:</label>
                <select name="available">
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select><br>
                <button type="submit">Add Food Item</button>
            </form>
        </div>
        
        <table>
            <tr>
                <th>Item ID</th>
                <th>Name</th>
                <th>Price</th>
                <th>Availability</th>
                <th>Actions</th>
            </tr>
            <?php while ($item = $food_items->fetch_assoc()): ?>
                <tr>
                    <form method="POST" action="update_food.php">
                        <td><?php echo htmlspecialchars($item['item_id']); ?></td>
                        <td><input type="text" name="name" value="<?php echo htmlspecialchars($item['name']); ?>"></td>
                        <td><input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($item['price']); ?>"></td>
                        <td>
                            <select name="available">
                                <option value="1" <?php if ($item['available'] == 1) echo 'selected'; ?>>Yes</option>
                                <option value="0" <?php if ($item['available'] == 0) echo 'selected'; ?>>No</option>
                            </select>
                        </td>
                        <td class="action-buttons">
                            <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
                            <button type="submit">Update</button>
                    </form>
                    <form method="POST" action="delete_food.php">
                        <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
                        <button type="submit" class="delete-button">Delete</button>
                    </form>
                        </td>
                </tr>
            <?php endwhile; ?>
        </table>

        <!-- Manage Orders Section -->
        <h2>Manage Orders</h2>
        <table>
            <tr>
                <th>Order ID</th>
                <th>User ID</th>
                <th>Status</th>
                <th>Order Type</th>
                <th>Order Date</th>
                <th>Actions</th>
            </tr>
            <?php while ($order = $orders->fetch_assoc()): ?>
                <tr>
                    <form method="POST" action="update_order.php">
                        <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                        <td><?php echo htmlspecialchars($order['user_id']); ?></td>
                        <td>
                            <select name="order_status">
                                <option value="Pending" <?php if ($order['order_status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                                <option value="Delivering" <?php if ($order['order_status'] == 'Delivering') echo 'selected'; ?>>Delivering</option>
                                <option value="Completed" <?php if ($order['order_status'] == 'Completed') echo 'selected'; ?>>Completed</option>
                                <option value="Cancelled" <?php if ($order['order_status'] == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
                            </select>
                        </td>
                        <td>
                            <select name="order_type">
                                <option value="Dine-In" <?php if ($order['order_type'] == 'Dine-In') echo 'selected'; ?>>Dine-in</option>
                                <option value="Take-Away" <?php if ($order['order_type'] == 'Take-Away') echo 'selected'; ?>>Takeaway</option>
                            </select>
                        </td>
                        <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                        <td>
                            <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                            <button type="submit">Update</button>
                        </td>
                    </form>
                </tr>
            <?php endwhile; ?>
        </table>

        <!-- Manage Users Section -->
        <h2>Manage Users</h2>
        <div class="admin-section">
            <form method="POST" action="add_user.php">
                <h3>Add New User</h3>
                <label for="name">Name:</label>
                <input type="text" name="name" required><br>
                <label for="email">Email:</label>
                <input type="email" name="email" required><br>
                <label for="password">Password:</label>
                <input type="password" name="password" required><br>
                <label for="role">Role:</label>
                <select name="role">
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                    <option value="staff">Staff</option>
                </select><br>
                <button type="submit">Add User</button>
            </form>
        </div>
        
        <table>
            <tr>
                <th>User ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
            <?php while ($user = $users->fetch_assoc()): ?>
                <tr>
                    <form method="POST" action="update_user.php">
                        <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                        <td><input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>"></td>
                        <td><input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>"></td>
                        <td>
                            <select name="role">
                                <option value="user" <?php if ($user['role'] == 'user') echo 'selected'; ?>>User</option>
                                <option value="admin" <?php if ($user['role'] == 'admin') echo 'selected'; ?>>Admin</option>
                                <option value="staff" <?php if ($user['role'] == 'staff') echo 'selected'; ?>>Staff</option>
                            </select>
                        </td>
                        <td>
                            <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                            <label for="password">New Password:</label>
                            <input type="password" name="new_password" placeholder="Leave blank to keep current password"><br>
                            <button type="submit">Update</button>
                        </td>
                    </form>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>

