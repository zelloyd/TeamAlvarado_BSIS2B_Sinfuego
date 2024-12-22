<?php
// Database connection
$host = "localhost";
$username = "root";
$password = "";
$dbname = "zaikicks";

$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch items from the database
$sql = "SELECT item_id, item_name, item_descrip, item_price, stock, image_path FROM items";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping List</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Shopping List</h1>
        <?php if ($result->num_rows > 0): ?>
            <div class="row">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-300">
                            <!-- Check and display the image -->
                            <?php if (!empty($row['image_path']) && $row['image_path'] !== 'No Image'): ?>
                                <img src="admin/<?= htmlspecialchars($row['image_path']) ?>" 
                                     class="card-img-top" 
                                     alt="<?= htmlspecialchars($row['item_name']) ?>" 
                                     style="height: 200px; object-fit: cover;">
                            <?php else: ?>
                                <img src="admin/uploads/placeholder.png" 
                                     class="card-img-top" 
                                     alt="No Image" 
                                     style="height: 200px; object-fit: cover;">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($row['item_name']) ?></h5>
                                <p class="card-text"><?= htmlspecialchars($row['item_descrip']) ?></p>
                                <p class="card-text"><strong>Price:</strong> ₱<?= number_format($row['item_price'], 2) ?></p>
                                <p class="card-text"><strong>Stock:</strong> <?= htmlspecialchars($row['stock']) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="text-center">No items found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
<?php
$conn->close();
?>
