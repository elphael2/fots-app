<?php
include 'includes/db.php';
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Secure password hashing

    // Check if the email already exists
    $checkEmail = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $checkEmail->store_result();

    if ($checkEmail->num_rows > 0) {
        $message = "Email already registered. Please use another email.";
    } else {
        // Insert the new user into the database
        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $password);

        if ($stmt->execute()) {
            $message = "Registration successful! You can now <a href='login.php'>log in</a>.";
        } else {
            $message = "Error: " . $stmt->error;
        }

        $stmt->close();
    }

    $checkEmail->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="css/styles.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        .message-box {
            margin-bottom: 15px;
            color: #d9534f;
            text-align: center;
        }
    </style>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    <div class="main-content">
        <h1>Register</h1>
        <?php if (!empty($message)): ?>
            <div class="message-box">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="register.php">
            <label for="name">Name:</label>
            <input type="text" name="name" required><br>
            <label for="email">Email:</label>
            <input type="email" name="email" required><br>
            <label for="password">Password:</label>
            <input type="password" name="password" required><br>
            <button type="submit">Register</button>
        </form>
    </div>
</body>
</html>
