<?php
session_start();
include 'includes/db.php';

// Check if the user is logged in and has staff privileges
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.php");
    exit;
}

$message = "";
$messageClass = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];
    $staff_id = $_SESSION['user_id'];

    // Assign the order to the staff
    $stmt = $conn->prepare("UPDATE orders SET staff_id = ?, order_status = 'Delivering' WHERE order_id = ?");
    $stmt->bind_param("ii", $staff_id, $order_id);

    if ($stmt->execute()) {
        $message = "Order assigned successfully.";
        $messageClass = "success";
    } else {
        $message = "Failed to assign the order: " . $stmt->error;
        $messageClass = "error";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Order</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 80%;
            max-width: 400px;
        }

        h1 {
            font-size: 1.5em;
            margin-bottom: 20px;
            color: #333;
        }

        .message {
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            font-size: 1em;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        a {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #28a745;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }

    </style>
</head>
<body>
    <div class="container">
        <h1>Order Assignment</h1>
        <div class="message <?php echo $messageClass; ?>">
            <?php echo $message; ?>
        </div>
        <a href="staff_dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
