<?php 
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "zaikicks";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Approve and Reject actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['order_id'])) {
        $order_id = intval($_POST['order_id']); // Ensure order_id is an integer

        if (isset($_POST['approve'])) {
            // Generate a tracking number (you can change the logic to something more complex if needed)
            $tracking_number = strtoupper(uniqid('TRACK-', true));
            // Update the order status to 'shipping' and set a tracking number
            $updateQuery = "UPDATE checkout SET status = 'shipping', tracking_number = '$tracking_number' WHERE order_id = $order_id";
        } elseif (isset($_POST['reject'])) {
            // Reject the order
            $updateQuery = "UPDATE checkout SET status = 'rejected' WHERE order_id = $order_id";
        }

        if (isset($updateQuery) && $conn->query($updateQuery) === TRUE) {
            echo "<script>alert('Order updated successfully'); window.location.href=window.location.href;</script>";
        } else {
            echo "Error updating order: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Management</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f4f4f4;
    }
    .container {
      max-width: 1200px;
      margin: 20px auto;
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    h1 {
      text-align: center;
      margin-bottom: 20px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    th, td {
      border: 1px solid #ddd;
      padding: 10px;
      text-align: left;
    }
    th {
      background-color: #6a1b9a;
      color: white;
    }
    .btn {
      padding: 8px 12px;
      background-color: #6a1b9a;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    .btn:hover {
      background-color: #4a148c;
    }
    .btn-danger {
      background-color: #e53935;
    }
    .btn-danger:hover {
      background-color: #b71c1c;
    }
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0, 0, 0, 0.5);
      justify-content: center;
      align-items: center;
    }
    .modal-content {
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      width: 80%;
      max-width: 600px;
    }
    .close-btn {
      float: right;
      font-size: 20px;
      font-weight: bold;
      cursor: pointer;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Order Management</h1>
    <table>
      <thead>
        <tr>
          <th>Order ID</th>
          <th>Username</th>
          <th>Total Price</th>
          <th>Status</th>
          <th>Order Date</th>
          <th>Tracking Number</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php
      // Fetch orders with pending or shipping status
      $sql = "
          SELECT checkout.order_id, checkout.total_price, checkout.status, checkout.created_at, checkout.tracking_number, users.username 
          FROM checkout 
          JOIN users ON checkout.username = users.username 
          WHERE checkout.status IN ('pending', 'shipping')";
      $result = $conn->query($sql);

      if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
              echo "<tr>";
              echo "<td>" . htmlspecialchars($row['order_id']) . "</td>";
              echo "<td>" . htmlspecialchars($row['username']) . "</td>";
              echo "<td>$" . htmlspecialchars($row['total_price']) . "</td>";
              echo "<td>" . htmlspecialchars($row['status']) . "</td>";
              echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
              // Display tracking number for shipping orders
              if ($row['status'] == 'shipping') {
                  echo "<td>" . htmlspecialchars($row['tracking_number']) . "</td>";
              } else {
                  echo "<td>N/A</td>";
              }
              echo "<td>
                <button class='btn' onclick='showOrderDetails(" . htmlspecialchars($row['order_id']) . ")'>View Details</button>
                <form method='POST' style='display: inline-block;'>
                  <input type='hidden' name='order_id' value='" . htmlspecialchars($row['order_id']) . "'>
                  <button class='btn' type='submit' name='approve'>Approve</button>
                </form>
                <form method='POST' style='display: inline-block;'>
                  <input type='hidden' name='order_id' value='" . htmlspecialchars($row['order_id']) . "'>
                  <button class='btn btn-danger' type='submit' name='reject'>Reject</button>
                </form>
              </td>";
              echo "</tr>";
          }
      } else {
          echo "<tr><td colspan='7'>No pending or shipping orders found.</td></tr>";
      }
      ?>
      </tbody>
    </table>
  </div>

  <!-- Modal for Order Details -->
  <div id="orderModal" class="modal">
    <div class="modal-content">
      <span class="close-btn" onclick="closeModal()">&times;</span>
      <h2>Order Details</h2>
      <div id="orderDetails"></div>
    </div>
  </div>

  <script>
    function showOrderDetails(orderId) {
      // Fetch the order details using AJAX
      fetch(`?order_id=${orderId}`)
        .then(response => response.text())
        .then(data => {
          document.getElementById("orderDetails").innerHTML = data;
          document.getElementById("orderModal").style.display = "flex";
        });
    }

    function closeModal() {
      document.getElementById("orderModal").style.display = "none";
    }
  </script>

  <?php
  // Fetch and display order details when order_id is passed
  if (isset($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);
    $sql = "SELECT item_name, quantity, price FROM order_items WHERE order_id = $order_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<table><tr><th>Item Name</th><th>Quantity</th><th>Price</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>" . htmlspecialchars($row['item_name']) . "</td>
                  <td>" . htmlspecialchars($row['quantity']) . "</td>
                  <td>$" . htmlspecialchars($row['price']) . "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "No order details found.";
    }
  }
  ?>

</body>
</html>

<?php
$conn->close();
?>
