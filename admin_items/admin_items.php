<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "zaikicks";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch items for display
function fetchItems($conn) {
    $sql = "SELECT * FROM items";
    $result = $conn->query($sql);
    $items = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
    }
    return $items;
}

// Handle adding an item with image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax']) && $_POST['ajax'] === '1') {
    $name = $conn->real_escape_string($_POST['item_name']);
    $description = $conn->real_escape_string($_POST['item_descrip']);
    $price = floatval($_POST['item_price']);
    $stock = intval($_POST['stock']);
    $imagePath = 'No Image';

    // Image upload handling
    if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] == UPLOAD_ERR_OK) {
        $imageTmpPath = $_FILES['item_image']['tmp_name'];
        $imageName = basename($_FILES['item_image']['name']);
        $imagePath = 'uploads/'. $imageName;

        // Ensure uploads directory exists
        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }

        if (!move_uploaded_file($imageTmpPath, $imagePath)) {
            echo json_encode(['error' => 'Failed to upload image.']);
            exit;
        }
    }

    // Insert into database
    $sql = "INSERT INTO items (item_name, item_descrip, item_price, stock, image_path) 
            VALUES ('$name', '$description', $price, $stock, '$imagePath')";

    if ($conn->query($sql) === TRUE) {
        $id = $conn->insert_id;
        echo json_encode([
            'id' => $id,
            'item_name' => $name,
            'item_descrip' => $description,
            'item_price' => $price,
            'stock' => $stock,
            'image_path' => $imagePath
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => $conn->error]);
    }
    exit;
}

// Handle item deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_item_id'])) {
    $item_id = intval($_POST['delete_item_id']);
    $sql = "DELETE FROM items WHERE item_id = $item_id";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => $conn->error]);
    }
    exit;
}

$items = fetchItems($conn);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Items Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            margin: 20px auto;
            width: 90%;
            max-width: 1000px;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #6a1b9a;
            color: #fff;
        }
        img {
            max-width: 50px;
            max-height: 50px;
            border-radius: 5px;
        }
        .btn {
            padding: 8px 12px;
            background-color: #6a1b9a;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-danger {
            background-color: #e53935;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Items Management</h1>
    <table>
        <thead>
        <tr>
            <th>Item Name</th>
            <th>Description</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Image</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody id="itemsTable">
        <?php foreach ($items as $item): ?>
            <tr data-id="<?= $item['item_id'] ?>">
                <td><?= htmlspecialchars($item['item_name']) ?></td>
                <td><?= htmlspecialchars($item['item_descrip']) ?></td>
                <td>$<?= number_format($item['item_price'], 2) ?></td>
                <td><?= htmlspecialchars($item['stock']) ?></td>
                <td>
                    <?php if ($item['image_path'] !== 'No Image'): ?>
                        <img src="<?= htmlspecialchars($item['image_path']) ?>" alt="Image">
                    <?php else: ?>
                        No Image
                    <?php endif; ?>
                </td>
                <td><button class="btn btn-danger" onclick="deleteItem(this)">Delete</button></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Form for adding an item -->
    <form id="addItemForm" enctype="multipart/form-data">
        <input type="text" name="item_name" placeholder="Item Name" required>
        <input type="text" name="item_descrip" placeholder="Description" required>
        <input type="number" step="0.01" name="item_price" placeholder="Price" required>
        <input type="number" name="stock" placeholder="Stock" required>
        <input type="file" name="item_image" accept="image/*">
        <button type="button" class="btn" onclick="addItem()">Add Item</button>
    </form>
</div>

<script>
function addItem() {
    const form = document.getElementById('addItemForm');
    const formData = new FormData(form);
    formData.append('ajax', '1');

    fetch('', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            alert(data.error);
            return;
        }

        const table = document.getElementById('itemsTable');
        const row = document.createElement('tr');
        row.dataset.id = data.id;

        row.innerHTML = `
            <td>${data.item_name}</td>
            <td>${data.item_descrip}</td>
            <td>$${parseFloat(data.item_price).toFixed(2)}</td>
            <td>${data.stock}</td>
            <td>
                ${data.image_path !== 'No Image' ? `<img src="${data.image_path}" alt="Image">` : 'No Image'}
            </td>
            <td><button class="btn btn-danger" onclick="deleteItem(this)">Delete</button></td>
        `;
        table.appendChild(row);

        form.reset();
    })
    .catch(error => console.error('Error:', error));
}

function deleteItem(button) {
    const row = button.parentElement.parentElement;
    const itemId = row.dataset.id;

    if (confirm('Are you sure you want to delete this item?')) {
        fetch('', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ delete_item_id: itemId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) row.remove();
            else alert('Failed to delete item.');
        })
        .catch(error => console.error('Error:', error));
    }
}
</script>
</body>
</html>
