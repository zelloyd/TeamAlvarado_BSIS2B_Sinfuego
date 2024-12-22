<?php 
session_start();

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

// Update order status
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];

    // Update status directly (delivered means final status)
    $sql_update = "UPDATE checkout SET status = ? WHERE order_id = ?";
    $stmt = $conn->prepare($sql_update);
    $stmt->bind_param("si", $new_status, $order_id);
    $stmt->execute();
}

// Fetch all orders with their details
$sql_orders = "SELECT c.order_id, c.username, c.status, c.total_price, c.tracking_number, 
                      c.created_at, c.delivery_address, c.payment_method
               FROM checkout c
               ORDER BY c.created_at DESC";
$result_orders = $conn->query($sql_orders);

// Prepare to fetch order items for each order
$sql_items = "SELECT * FROM order_items WHERE order_id = ?";
$stmt_items = $conn->prepare($sql_items);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Order Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #4a148c;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        table th {
            background-color: #4a148c;
            color: white;
        }
        .order-items {
            background-color: #f7f7f7;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
        }
        .order-items ul {
            padding-left: 20px;
        }
        .order-items ul li {
            margin-bottom: 5px;
        }
        form {
            margin: 0;
        }
        select {
            padding: 5px;
            font-size: 14px;
        }
        button {
            background-color: #6a1b9a;
            color: white;
            border: none;
            padding: 8px 15px;
            font-size: 14px;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #4a148c;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Order Management</h1>

        <?php if ($result_orders->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Username</th>
                        <th>Delivery Address</th>
                        <th>Payment Method</th>
                        <th>Total Price</th>
                        <th>Status</th>
                        <th>Order Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order = $result_orders->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $order['order_id']; ?></td>
                            <td><?php echo $order['username']; ?></td>
                            <td><?php echo $order['delivery_address']; ?></td>
                            <td><?php echo ucfirst($order['payment_method']); ?></td>
                            <td>$<?php echo number_format($order['total_price'], 2); ?></td>
                            <td><?php echo ucfirst($order['status']); ?></td>
                            <td><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></td>
                            <td>
                                <form action="" method="POST">
                                    <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                    <select name="status">
                                        <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                        <option value="delivered" <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                    </select>
                                    <button type="submit">Update</button>
                                </form>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="8">
                                <div class="order-items">
                                    <h4>Items Ordered:</h4>
                                    <ul>
                                        <?php
                                        $stmt_items->bind_param("i", $order['order_id']);
                                        $stmt_items->execute();
                                        $result_items = $stmt_items->get_result();
                                        while ($item = $result_items->fetch_assoc()): ?>
                                            <li><?php echo $item['item_name']; ?> - $<?php echo number_format($item['price'], 2); ?> x <?php echo $item['quantity']; ?></li>
                                        <?php endwhile; ?>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No orders found.</p>
        <?php endif; ?>

        <?php 
        $stmt_items->close();
        $conn->close();
        ?>
    </div>
</body>
</html>
