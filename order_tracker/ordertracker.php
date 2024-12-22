<?php 
// Start session to track login status
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to login page if the user is not logged in
    header("Location: login.php");
    exit();
}

// Database connection
$host = 'localhost';
$db = 'zaikicks';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the username from the session
$username = $_SESSION['username'];

// Fetch the orders for the logged-in user, grouped by order status
$sql = "SELECT checkout.order_id, checkout.status, checkout.total_price, checkout.created_at
        FROM checkout
        WHERE checkout.username = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username); // Bind username as a string
$stmt->execute();
$result = $stmt->get_result();

// Organize orders by status
$orders_by_status = [
    'pending' => [],
    'shipped' => [],
    'delivered' => []
];

while ($row = $result->fetch_assoc()) {
    $orders_by_status[$row['status']][] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Tracking</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .navbar {
            background-color: #009688;
            padding: 15px;
            color: white;
            text-align: center;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
        }

        .container {
            padding: 30px;
        }

        .order-section {
            margin-bottom: 30px;
        }

        .order-section h2 {
            color: #009688;
            border-bottom: 2px solid #009688;
            padding-bottom: 10px;
        }

        .order-card {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 15px;
        }

        .order-card p {
            margin: 5px 0;
        }

        .footer {
            background-color: #009688;
            color: white;
            text-align: center;
            padding: 10px;
            position: fixed;
            width: 100%;
            bottom: 0;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <h1>Order Tracking</h1>
    <a href="logout.php">Logout</a>
    <a href="homepageclient.php">Home</a>
</div>

<!-- Main Content -->
<div class="container">
    <h2>Your Orders</h2>

    <!-- Pending Orders -->
    <div class="order-section">
        <h2>Pending Orders</h2>
        <?php if (empty($orders_by_status['pending'])): ?>
            <p>You have no pending orders.</p>
        <?php else: ?>
            <?php foreach ($orders_by_status['pending'] as $order): ?>
                <div class="order-card">
                    <p><strong>Order ID:</strong> <?php echo htmlspecialchars($order['order_id']); ?></p>
                    <p><strong>Status:</strong> <?php echo htmlspecialchars($order['status']); ?></p>
                    <p><strong>Total Price:</strong> $<?php echo number_format($order['total_price'], 2); ?></p>
                    <p><strong>Order Date:</strong> <?php echo htmlspecialchars($order['created_at']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Shipped Orders -->
    <div class="order-section">
        <h2>Shipped Orders</h2>
        <?php if (empty($orders_by_status['shipped'])): ?>
            <p>You have no shipped orders.</p>
        <?php else: ?>
            <?php foreach ($orders_by_status['shipped'] as $order): ?>
                <div class="order-card">
                    <p><strong>Order ID:</strong> <?php echo htmlspecialchars($order['order_id']); ?></p>
                    <p><strong>Status:</strong> <?php echo htmlspecialchars($order['status']); ?></p>
                    <p><strong>Total Price:</strong> $<?php echo number_format($order['total_price'], 2); ?></p>
                    <p><strong>Order Date:</strong> <?php echo htmlspecialchars($order['created_at']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Completed Orders -->
    <div class="order-section">
        <h2>Completed Orders</h2>
        <?php if (empty($orders_by_status['delivered'])): ?>
            <p>You have no completed orders.</p>
        <?php else: ?>
            <?php foreach ($orders_by_status['delivered'] as $order): ?>
                <div class="order-card">
                    <p><strong>Order ID:</strong> <?php echo htmlspecialchars($order['order_id']); ?></p>
                    <p><strong>Status:</strong> <?php echo htmlspecialchars($order['status']); ?></p>
                    <p><strong>Total Price:</strong> $<?php echo number_format($order['total_price'], 2); ?></p>
                    <p><strong>Order Date:</strong> <?php echo htmlspecialchars($order['created_at']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Footer -->
<div class="footer">
    <p>&copy; 2024 Zaikicks | Order Tracking</p>
</div>

</body>
</html>
