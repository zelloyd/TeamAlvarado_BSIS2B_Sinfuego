<?php
session_start(); // Start the session to access cart data

// Redirect to order page if the cart is empty
if (empty($_SESSION['cart'])) {
    header("Location: orderpage.php");
    exit;
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

// Fetch item details from the cart
$cart_items = [];
if (!empty($_SESSION['cart'])) {
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
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart - Checkout</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; }
        .container { padding: 30px; }
        .item-card { background-color: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); margin-bottom: 20px; }
        .item-card img { max-width: 100px; max-height: 100px; margin-right: 20px; }
        .item-details { display: flex; align-items: center; }
        .summary { margin-top: 20px; padding: 20px; background-color: #fff; border-radius: 8px; }
        .summary h3 { margin: 0; }
    </style>
</head>
<body>

<div class="container">
    <h1>Cart - Checkout</h1>

    <?php if (!empty($cart_items)): ?>
        <?php
        $total_price = 0;
        foreach ($cart_items as $item):
            $item_total = $item['item_price'] * $item['quantity'];
            $total_price += $item_total;
        ?>
            <div class="item-card">
                <div class="item-details">
                    <img src="admin/<?php echo htmlspecialchars($item['image_path']); ?>" alt="Item Image">
                    <div>
                        <h3><?php echo htmlspecialchars($item['item_name']); ?></h3>
                        <p>Price: PHP <?php echo number_format($item['item_price'], 2); ?></p>
                        <p>Quantity: <?php echo $item['quantity']; ?></p>
                        <p>Total: PHP <?php echo number_format($item_total, 2); ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="summary">
            <h3>Total Price: PHP <?php echo number_format($total_price, 2); ?></h3>
            <a href="order_confirmation.php" class="btn">Confirm Order</a>
        </div>
    <?php else: ?>
        <p>Your cart is empty.</p>
    <?php endif; ?>
</div>

</body>
</html>

<?php
$conn->close();
?>