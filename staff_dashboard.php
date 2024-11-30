<?php
session_start();
include 'includes/db.php';

// Check if the user is logged in and has staff privileges
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch the order currently assigned to this staff
$current_order = $conn->query("SELECT o.*, u.address FROM orders o 
                               JOIN users u ON o.user_id = u.user_id 
                               WHERE o.staff_id = $user_id AND o.order_status = 'Delivering'")->fetch_assoc();

// Fetch all available orders if no order is currently assigned
$orders = !$current_order ? $conn->query("SELECT * FROM orders WHERE order_status = 'Pending' AND order_type  != 'Dine-In'") : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <style>
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
        .center-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        #map {
            width: 100%;
            height: 400px;
            margin-top: 20px;
            border-radius: 10px;
            display: none;
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
        .button-group {
            display: inline-flex;
            gap: 10px;
        }
        button {
            margin: 0;
            padding: 10px 15px;
            font-size: 16px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #218838;
        }
    </style>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        let map, userMarker, staffMarker;

        function initMap(address) {
            if (!address) return;

            document.getElementById("map").style.display = "block"; // Show map

            map = L.map('map').setView([0, 0], 15); // Default initial view

            // Load OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // Geocode the address to get coordinates
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.length > 0) {
                        const lat = data[0].lat;
                        const lon = data[0].lon;
                        map.setView([lat, lon], 15);

                        // Place marker at the geocoded location for the user
                        userMarker = L.marker([lat, lon])
                            .addTo(map)
                            .bindPopup("<b>User's Address</b><br>" + address)
                            .openPopup();
                    } else {
                        alert("Unable to find location for the address.");
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function updateLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const latitude = position.coords.latitude;
                    const longitude = position.coords.longitude;

                    // Remove previous staff marker if exists
                    if (staffMarker) {
                        map.removeLayer(staffMarker);
                    }

                    // Add a new marker for the current location of the staff
                    staffMarker = L.marker([latitude, longitude])
                        .addTo(map)
                        .bindPopup("<b>Your Current Location</b>")
                        .openPopup();

                    // Center map on staff location
                    map.setView([latitude, longitude], 15);

                    // Send the coordinates to the server
                    fetch('update_location.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `latitude=${latitude}&longitude=${longitude}&order_id=<?php echo $current_order['order_id'] ?? ''; ?>`
                    })
                    .then(response => response.text())
                    .then(data => {
                        alert('Location updated successfully');
                    })
                    .catch(error => {
                        alert('Failed to update location');
                        console.error('Error:', error);
                    });
                });
            } else {
                alert("Geolocation is not supported by this browser.");
            }
        }
    </script>
</head>
<body>
    <!-- Logout Button -->
    <a href="logout.php" class="logout-button">Logout</a>

    <div class="center-container">
        <h1>Staff Dashboard</h1>
        <?php if ($current_order): ?>
            <h2>Currently Assigned Order</h2>
            <p><strong>Order ID:</strong> <?php echo htmlspecialchars($current_order['order_id']); ?></p>
            <p><strong>User's Address:</strong> <?php echo htmlspecialchars($current_order['address']); ?></p>
            <button onclick="updateLocation()">Update My Location</button>

            <div id="map"></div>
            <script>
                // Initialize the map with the user's address when the page loads
                initMap("<?php echo htmlspecialchars($current_order['address']); ?>");
            </script>

            <div class="button-group">
                <!-- Complete Order Button -->
                <form method="POST" action="complete_order.php">
                    <input type="hidden" name="order_id" value="<?php echo $current_order['order_id']; ?>">
                    <button type="submit">Complete Order</button>
                </form>

                <!-- Cancel Assignment Button -->
                <form method="POST" action="cancel_assignment.php">
                    <input type="hidden" name="order_id" value="<?php echo $current_order['order_id']; ?>">
                    <button type="submit">Cancel Assignment</button>
                </form>
            </div>
        <?php else: ?>
            <h2>Available Orders</h2>
            <table>
                <tr>
                    <th>Order ID</th>
                    <th>User ID</th>
                    <th>Status</th>
                    <th>Order Date</th>
                    <th>Actions</th>
                </tr>
                <?php while ($order = $orders->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                        <td><?php echo htmlspecialchars($order['user_id']); ?></td>
                        <td><?php echo htmlspecialchars($order['order_status']); ?></td>
                        <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                        <td>
                            <form method="POST" action="assign_order.php">
                                <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                <button type="submit">Assign to Me</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>

