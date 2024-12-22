<?php
// Starting session for admin authentication
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "zaikicks";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all orders with client info (username, status, total price, etc.)
// Make sure to use the correct column name for order ID (replace c.id with the correct column name)
$sql = "
    SELECT c.order_id, c.username, c.status, c.total_price, c.tracking_number, c.created_at, 
           u.email AS client_email
    FROM checkout c
    JOIN users u ON c.username = u.username
    ORDER BY c.created_at DESC
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Orders</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #6a1b9a;
            color: white;
            padding: 20px;
            text-align: center;
        }
        nav {
            background-color: #4a148c;
            overflow: hidden;
        }
        nav a {
            float: left;
            display: block;
            color: white;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
        }
        nav a:hover {
            background-color: #6a1b9a;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .orders-table {
            width: 100%;
            border-collapse: collapse;
        }
        .orders-table th, .orders-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .orders-table th {
            background-color: #6a1b9a;
            color: white;
        }
        .order-card {
            margin-bottom: 20px;
            background-color: #f4f4f4;
            padding: 20px;
            border-radius: 8px;
        }
        footer {
            text-align: center;
            padding: 20px;
            background-color: #6a1b9a;
            color: white;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<header>
    <h1>Admin - Orders</h1>
</header>

<nav>
    <a href="admindashboard.php">Dashboard</a>
    <a href="admin_orders.php">Order Management</a>
    <a href="admin_items.php">Items Management</a>
    <a href="admin_order_add.php">Order Information</a>
    <a href="logoutadmin.php">Logout</a>
</nav>

<div class="container">
    <h2>Order List</h2>
    <table class="orders-table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Username</th>
                <th>Status</th>
                <th>Total Price</th>
                <th>Tracking Number</th>
                <th>Order Date</th>
                <th>Client Email</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // If there are orders in the database, display them
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['order_id'] . "</td>";
                    echo "<td>" . $row['username'] . "</td>";
                    echo "<td>" . ucfirst($row['status']) . "</td>";
                    echo "<td>$" . number_format($row['total_price'], 2) . "</td>";
                    echo "<td>" . $row['tracking_number'] . "</td>";
                    echo "<td>" . $row['created_at'] . "</td>";
                    echo "<td>" . $row['client_email'] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>No orders found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<footer>
    <p>Admin Panel &copy; 2024 Zaikicks</p>
</footer>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
