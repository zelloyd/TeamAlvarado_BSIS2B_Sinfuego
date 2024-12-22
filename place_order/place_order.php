<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_order'])) {
    // Database connection
    $host = 'localhost';
    $db = 'zaikicks';
    $user = 'root';
    $pass = '';
    $conn = new mysqli($host, $user, $pass, $db);

    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    // Check if cart exists and is not empty
    if (empty($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        echo "Your cart is empty. Please add items before placing an order.";
        exit;
    }

    // Get the user ID, default to 0 for guest users
    $user_id = $_SESSION['user_id'] ?? 0;
    $total_price = 0;

    // Calculate total price from cart items
    foreach ($_SESSION['cart'] as $cart_item) {
        $total_price += $cart_item['quantity'] * $cart_item['item_price'];
    }

    // Insert the order into the `checkout` table
    $stmt = $conn->prepare("INSERT INTO checkout (user_id, total_price) VALUES (?, ?)");
    $stmt->bind_param("id", $user_id, $total_price);

    if ($stmt->execute()) {
        $order_id = $stmt->insert_id; // Get the ID of the newly created order

        // Insert each cart item into the `order_items` table
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, item_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach ($_SESSION['cart'] as $cart_item) {
            $item_id = $cart_item['item_id'];
            $quantity = $cart_item['quantity'];
            $price = $cart_item['item_price'];
            $stmt->bind_param("iiid", $order_id, $item_id, $quantity, $price);
            $stmt->execute();
        }

        // Clear the cart after successful order placement
        $_SESSION['cart'] = [];

        // Redirect to a success page
        header("Location: success.php");
        exit;
    } else {
        // Display error if the order insertion fails
        echo "Error: " . $stmt->error;
    }

    // Close the database connection
    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
