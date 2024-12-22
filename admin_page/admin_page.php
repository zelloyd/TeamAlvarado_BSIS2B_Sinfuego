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

// Fetch dashboard data

// Total Sales: Only count 'completed' or 'paid' or 'delivered' orders for sales
$sql_sales = "SELECT SUM(total_price) AS total_sales FROM checkout WHERE status IN ('completed', 'paid', 'delivered')";
$result_sales = $conn->query($sql_sales);
$total_sales = $result_sales->fetch_assoc()['total_sales'] ?: 0;

// Total Orders
$sql_orders = "SELECT COUNT(*) AS total_orders FROM checkout";
$result_orders = $conn->query($sql_orders);
$total_orders = $result_orders->fetch_assoc()['total_orders'] ?: 0;

// Total Users
$sql_users = "SELECT COUNT(*) AS total_users FROM users";
$result_users = $conn->query($sql_users);
$total_users = $result_users->fetch_assoc()['total_users'] ?: 0;

// Total Inventory Items
$sql_inventory = "SELECT COUNT(*) AS total_items FROM items";
$result_inventory = $conn->query($sql_inventory);
$total_items = $result_inventory->fetch_assoc()['total_items'] ?: 0;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Homepage</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
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
        .dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        .card {
            background-color: #6a1b9a;
            color: white;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            font-size: 1.2em;
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
    <h1>Welcome, Admin</h1>
</header>

<nav>
    <a href="admindashboard.php">Dashboard</a>
    <a href="admin_orders.php">Order Management</a>
    <a href="admin_items.php">Items Management</a>
    <a href="admin_order_add.php">Ordered Information</a>
    <a href="stats_orders.php">List Of Orders</a>
    <a href="logoutadmin.php">Logout</a>
</nav>

<div class="container">
    <h2>Quick Stats</h2>
    <div class="dashboard">
        <div class="card">
            <h3>Total Sales</h3>
            <p>$<?php echo number_format($total_sales, 2); ?></p>
        </div>
        <div class="card">
            <h3>Total Orders</h3>
            <p><?php echo $total_orders; ?></p>
        </div>
        <div class="card">
            <h3>Total Users</h3>
            <p><?php echo $total_users; ?></p>
        </div>
        <div class="card">
            <h3>Total Inventory Items</h3>
            <p><?php echo $total_items; ?></p>
        </div>
    </div>
</div>

<footer>
    <p>Admin Panel &copy; 2024 Zaikicks</p>
</footer>

</body>
</html>
