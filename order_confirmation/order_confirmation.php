<?php   
session_start(); // Make sure session is started

// Database connection details
$host = 'localhost';
$db = 'zaikicks';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure the cart is not empty
if (empty($_SESSION['cart'])) {
    echo "<p>Your cart is empty. Please add items before confirming.</p>";
    exit;
}

// Fetch item details from the cart
$cart_items = [];
foreach ($_SESSION['cart'] as $cart_item) {
    $item_id = $cart_item['item_id'];
    $quantity = $cart_item['quantity'];

    $sql = "SELECT * FROM items WHERE item_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $item = $result->fetch_assoc();
        $item['quantity'] = $quantity; // Add quantity to the item details
        $cart_items[] = $item;
    }
}

// Handle order confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure user ID or username is available
    $user_id = $_SESSION['user_id'] ?? 0; // Default to 0 for guest users
    $user_name = $_SESSION['username'] ?? 'Guest'; // Default to 'Guest' for guests
    
    // Check if user ID or username is valid
    if ($user_id == 0 && $user_name == 'Guest') {
        echo "Error: You need to be logged in to place an order.";
        exit;
    }

    $total_price = 0;

    // Validate the delivery address and payment method
    $delivery_address = $_POST['delivery_address'] ?? null;
    $payment_method = $_POST['payment_method'] ?? null;

    if (empty($delivery_address) || empty($payment_method)) {
        echo "Error: Delivery address and payment method are required.";
        exit;
    }

    // Calculate total price
    foreach ($cart_items as $item) {
        $total_price += $item['item_price'] * $item['quantity'];
    }

    // Insert order into the `checkout` table
    $stmt = $conn->prepare("INSERT INTO checkout (user_id, username, total_price, delivery_address, payment_method) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isiss", $user_id, $user_name, $total_price, $delivery_address, $payment_method);
    
    if ($stmt->execute()) {
        $order_id = $stmt->insert_id;

        // Insert order details into `order_items`
        foreach ($cart_items as $item) {
            if (!isset($item['item_price']) || empty($item['item_price'])) {
                echo "Error: Price is missing for item " . $item['item_name'] . "<br>";
                continue; // Skip this item or handle accordingly
            }

            $stmt = $conn->prepare("INSERT INTO order_items (order_id, item_name, item_id, quantity, price) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("isdis", $order_id, $item['item_name'], $item['item_id'], $item['quantity'], $item['item_price']);
            
            if (!$stmt->execute()) {
                echo "Error executing statement: " . $stmt->error;
            }
        }

        // Clear the cart after successful order
        unset($_SESSION['cart']);

        // Redirect to a success page
        header("Location: success.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { padding: 30px; }
        .footer { background-color: #009688; color: white; text-align: center; padding: 10px; position: fixed; width: 100%; bottom: 0; }
    </style>
</head>
<body>

<div class="container">
    <h2>Confirm Your Order</h2>

    <h3>Order Summary</h3>
    <table border="1">
        <tr>
            <th>Item</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Total</th>
        </tr>
        <?php
        $total_price = 0;
        foreach ($cart_items as $item):
            $item_total = $item['item_price'] * $item['quantity'];
            $total_price += $item_total;
        ?>
            <tr>
                <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                <td><?php echo number_format($item['item_price'], 2); ?> PHP</td>
                <td><?php echo $item['quantity']; ?></td>
                <td><?php echo number_format($item_total, 2); ?> PHP</td>
            </tr>
        <?php endforeach; ?>
    </table>

    <p><strong>Total Price: PHP <?php echo number_format($total_price, 2); ?></strong></p>

    <!-- Form to confirm the order, including delivery address and payment method -->
    <form method="POST" action="">
        <label for="delivery_address">Delivery Address:</label>
        <input type="text" name="delivery_address" id="delivery_address" required><br><br>

        <label for="payment_method">Payment Method:</label>
        <select name="payment_method" id="payment_method" required>
            <option value="credit_card">Credit Card</option>
            <option value="paypal">PayPal</option>
            <option value="paymaya">PayMaya</option>
            <option value="Gcash">Gcash</option>
            <option value="cod">Cash on Delivery</option>
        </select><br><br>

        <button type="submit" name="confirm_order">Confirm Order</button>
    </form>
</div>

<div class="footer">
    <p>Zaikicks - Order Confirmation</p>
</div>

</body>
</html>
