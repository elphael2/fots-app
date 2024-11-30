<div class="sidebar">
    <h2>Menu</h2>
    <a href="index.php">Home</a>
    <a href="order.php">Order</a>
    <a href="view_orders.php">View Orders</a>
    <a href="cart.php">Cart</a>
    <?php if (!isset($_SESSION['user_id'])): ?>
        <a href="register.php">Register</a>
        <a href="login.php">Login</a>
    <?php else: ?>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    <?php endif; ?>
</div>

<style>
    .sidebar {
        position: fixed;
        width: 250px;
        height: 100vh;
        background-color: #28a745;
        padding: 20px;
        color: #fff;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        overflow-y: auto;
    }

    .sidebar h2 {
        margin-bottom: 20px;
        font-size: 24px;
        color: #d4edda;
    }

    .sidebar a {
        color: #fff;
        text-decoration: none;
        padding: 10px 15px;
        margin-bottom: 10px;
        width: 100%;
        border-radius: 5px;
        transition: background-color 0.3s, transform 0.2s;
    }

    .sidebar a:hover {
        background-color: #218838;
        transform: scale(1.05);
    }
</style>