<?php
session_start();
include 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT user_id, name, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($user_id, $name, $hashed_password, $role);

    if ($stmt->num_rows > 0) {
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) {
            // Set session variables
            $_SESSION['user_id'] = $user_id;
            $_SESSION['name'] = $name;
            $_SESSION['role'] = $role; // Add role to session

            // Redirect based on user role
            if ($role === 'admin') {
                header("Location: admin_dashboard.php"); // Admin-specific page
            } elseif ($role === 'staff') {
                header("Location: staff_dashboard.php"); // Staff-specific page
            } else {
                header("Location: profile.php"); // Regular user profile page
            }
            exit;
        } else {
            $error_message = "Invalid password.";
        }
    } else {
        $error_message = "No account found with that email.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="css/styles.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    <div class="main-content">
        <h1>Login</h1>
        <form method="POST" action="login.php">
            <label for="email">Email:</label>
            <input type="email" name="email" required><br>
            <label for="password">Password:</label>
            <input type="password" name="password" required><br>
            <button type="submit">Login</button>
        </form>
        <?php if (isset($error_message)): ?>
            <div style="text-align: center; margin-top: 20px; color: red;">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
