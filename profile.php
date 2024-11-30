<?php
session_start();
include 'includes/db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$stmt = $conn->prepare("SELECT name, email, address FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Handle form submission to update address
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['address'])) {
    $new_address = $_POST['address'];
    $stmt = $conn->prepare("UPDATE users SET address = ? WHERE user_id = ?");
    $stmt->bind_param("si", $new_address, $user_id);
    if ($stmt->execute()) {
        echo "<div style='text-align: center; margin-top: 20px; color: green;'>Address updated successfully.</div>";
    } else {
        echo "<div style='text-align: center; margin-top: 20px; color: red;'>Failed to update address. Please try again.</div>";
    }
    $stmt->close();

    // Refresh user details after updating
    $stmt = $conn->prepare("SELECT name, email, address FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        /* Container for sidebar and main content */
        .container {
            display: flex;
        }

        /* Sidebar styling */
        .sidebar {
            width: 250px; /* Sidebar fixed width */
            height: 100vh;
            background-color: #28a745;
            padding: 20px;
            color: #fff;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            position: fixed; /* Keep sidebar fixed */
            top: 0;
            left: 0;
        }

        /* Main content styling */
        .main-content {
            margin-left: 270px; /* Offset for sidebar width */
            max-width: 600px;
            padding: 40px;
            margin: 50px auto; /* Center horizontally */
            text-align: center;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        /* Form elements */
        form {
            margin-top: 20px;
        }
        textarea {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            resize: none;
        }
        button {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 15px;
        }
        button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php include 'includes/sidebar.php'; ?>
        <div class="main-content">
            <h1>Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h1>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($user['address'] ?? 'No address provided'); ?></p>
            
            <h2>Update Address</h2>
            <form method="POST" action="profile.php">
                <label for="address">New Address:</label><br>
                <textarea name="address" rows="3" required><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea><br>
                <button type="submit">Save Address</button>
            </form>
        </div>
    </div>
</body>
</html>


