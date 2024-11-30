<?php
session_start();
include 'includes/db.php';

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $new_password = $_POST['new_password'];

    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, password = ?, role = ? WHERE user_id = ?");
        $stmt->bind_param("ssssi", $name, $email, $hashed_password, $role, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, role = ? WHERE user_id = ?");
        $stmt->bind_param("sssi", $name, $email, $role, $user_id);
    }

    if ($stmt->execute()) {
        echo "User updated successfully.";
    } else {
        echo "Error updating user: " . $stmt->error;
    }
    $stmt->close();
}
header("Location: admin_dashboard.php");
?>
