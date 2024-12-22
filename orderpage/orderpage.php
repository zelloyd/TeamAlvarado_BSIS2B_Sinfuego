<?php    // Start the session to access session variables
session_start();
// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
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

// Fetch available items from the database
$sql = "SELECT * FROM items"; // Fetch all items including images
$result = $conn->query($sql);

// Check if the form is submitted to add items to the cart
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_id = $_POST['item_id'];
    $quantity = $_POST['quantity'];

    // Add item to the cart session
    $cart_item = [
        'item_id' => $item_id,
        'quantity' => $quantity
    ];

    // Add the item to the session cart
    $_SESSION['cart'][] = $cart_item;

    // Redirect to cart2.php for checkout
    header("Location: cart3.php");
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order page</title>
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

        .item-list {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .item-card {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .item-card img {
            max-width: 200px;
            max-height: 200px;
            margin-bottom: 10px;
            border-radius: 8px;
        }

        .item-card h3 {
            margin: 0;
        }

        .item-card p {
            font-size: 1.2em;
            color: #009688;
        }

        .item-card input {
            width: 100%;
            padding: 8px;
            margin: 10px 0;
            font-size: 1em;
            border: 1px solid #ccc;
            border-radius: 5px;
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
    <h1>Products</h1>
    <a href="logout.php">Logout</a>
    <a href="homepageclient.php">Home</a>
    <a href="ordertracker.php">Order</a>
    <a href="cart3.php">View Cart (<?php echo count($_SESSION['cart']); ?>)</a>
</div>

<!-- Main Content -->
<div class="container">
    <h2>Available Items</h2>

    <!-- Display items fetched from the database -->
    <div class="item-list">
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='item-card'>";

            // Check and display the image if available
            if (!empty($row['image_path']) && $row['image_path'] !== 'No Image') {
                // Use the full path from the image_path column
                echo "<img src='admin/" . htmlspecialchars($row['image_path']) . "' alt='Item Image'>";
            } else {
                echo "<img src='placeholder.png' alt='No Image'>";
            }

            echo "<h3>" . htmlspecialchars($row['item_name']) . "</h3>";
            echo "<p>Price: $" . htmlspecialchars($row['item_price']) . "</p>";

            // Form to add item to the cart
            echo "<form method='POST' action=''>";
            echo "<input type='number' name='quantity' min='1' placeholder='Quantity' required>";
            echo "<input type='hidden' name='item_id' value='" . htmlspecialchars($row['item_id']) . "'>";
            echo "<button type='submit' style='padding: 10px; background-color: #009688; color: white; border: none; border-radius: 5px; cursor: pointer;'>Add to cart</button>";
            echo "</form>";

            echo "</div>";
        }
    } else {
        echo "<p>No items available for order.</p>";
    }
    ?>
    </div>
</div>

</body>
</html>
