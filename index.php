<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Food Ordering App</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="welcome-screen" onclick="window.location.href='order.php';">
        <h1>Welcome to the Food Ordering App</h1>
        <p>Click anywhere to start ordering!</p>
    </div>
</body>
</html>

<style>
    body, html {
        height: 100%;
        width: 100%;
        margin: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        background: url('images/background_wc.jpg') no-repeat center center fixed;
        background-size: cover;
        cursor: pointer;
    }
    .welcome-screen {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        border: 2px dashed #28a745;
        border-radius: 10px;
        background-color: rgba(255, 255, 255, 0.8); /* Slight overlay */
        transition: background-color 0.3s, transform 0.2s;
    }
    .welcome-screen:hover {
        background-color: rgba(255, 255, 255, 0.9);
        transform: scale(1.02);
    }
</style>
