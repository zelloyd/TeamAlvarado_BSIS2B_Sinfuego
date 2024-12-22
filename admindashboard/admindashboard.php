<!DOCTYPE html>  
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - Reports</title>
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
    .report {
      margin-bottom: 40px;
    }
    .report h3 {
      background-color: #6a1b9a;
      color: white;
      padding: 10px;
      margin: 0;
      font-size: 1.2em;
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
  </style>
</head>
<body>
  <div class="container">
    <h1>Admin Reports Dashboard</h1>

    <!-- Sales Report Today vs Yesterday -->
    <div class="report">
      <h3>Sales Report - Today vs Yesterday</h3>
      <table>
        <thead>
          <tr>
            <th>Date</th>
            <th>Total Sales</th>
            <th>Store Profit</th>
          </tr>
        </thead>
        <tbody>
          <?php
          // Database connection
          $servername = "localhost";
          $username = "root";
          $password = "";
          $dbname = "zaikicks";

          $conn = new mysqli($servername, $username, $password, $dbname);

          if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
          }

          // Get sales for today and yesterday
          $today = date('Y-m-d');
          $yesterday = date('Y-m-d', strtotime('-1 day'));

          // Sales Today (Completed or Delivered)
          $sql_today = "SELECT SUM(total_price) AS sales_today FROM checkout WHERE DATE(created_at) = '$today' AND (status = 'completed' OR status = 'delivered')";
          $result_today = $conn->query($sql_today);
          $sales_today = $result_today->num_rows > 0 ? $result_today->fetch_assoc()['sales_today'] : 0;

          // Sales Yesterday (Completed or Delivered)
          $sql_yesterday = "SELECT SUM(total_price) AS sales_yesterday FROM checkout WHERE DATE(created_at) = '$yesterday' AND (status = 'completed' OR status = 'delivered')";
          $result_yesterday = $conn->query($sql_yesterday);
          $sales_yesterday = $result_yesterday->num_rows > 0 ? $result_yesterday->fetch_assoc()['sales_yesterday'] : 0;

          // Store Profit Today and Yesterday (based on completed and delivered sales)
          $profit_today = $sales_today;  
          $profit_yesterday = $sales_yesterday;

          echo "<tr><td>Today</td><td>$" . number_format($sales_today, 2) . "</td><td>$" . number_format($profit_today, 2) . "</td></tr>";
          echo "<tr><td>Yesterday</td><td>$" . number_format($sales_yesterday, 2) . "</td><td>$" . number_format($profit_yesterday, 2) . "</td></tr>";
          ?>

        </tbody>
      </table>
    </div>

    <!-- Sales This Year vs Last Year -->
    <div class="report">
      <h3>Sales Report - This Year vs Last Year</h3>
      <table>
        <thead>
          <tr>
            <th>Year</th>
            <th>Total Sales</th>
            <th>Store Profit</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $current_year = date('Y');
          $last_year = $current_year - 1;

          // Sales This Year (Completed or Delivered)
          $sql_this_year = "SELECT SUM(total_price) AS sales_this_year FROM checkout WHERE YEAR(created_at) = '$current_year' AND (status = 'completed' OR status = 'delivered')";
          $result_this_year = $conn->query($sql_this_year);
          $sales_this_year = $result_this_year->num_rows > 0 ? $result_this_year->fetch_assoc()['sales_this_year'] : 0;

          // Sales Last Year (Completed or Delivered)
          $sql_last_year = "SELECT SUM(total_price) AS sales_last_year FROM checkout WHERE YEAR(created_at) = '$last_year' AND (status = 'completed' OR status = 'delivered')";
          $result_last_year = $conn->query($sql_last_year);
          $sales_last_year = $result_last_year->num_rows > 0 ? $result_last_year->fetch_assoc()['sales_last_year'] : 0;

          // Store Profit This Year and Last Year (based on completed and delivered sales)
          $profit_this_year = $sales_this_year;  
          $profit_last_year = $sales_last_year;

          echo "<tr><td>$current_year</td><td>$" . number_format($sales_this_year, 2) . "</td><td>$" . number_format($profit_this_year, 2) . "</td></tr>";
          echo "<tr><td>$last_year</td><td>$" . number_format($sales_last_year, 2) . "</td><td>$" . number_format($profit_last_year, 2) . "</td></tr>";
          ?>
        </tbody>
      </table>
    </div>

    <!-- Inventory Report -->
    <div class="report">
      <h3>Inventory Report</h3>
      <table>
        <thead>
          <tr>
            <th>Product Name</th>
            <th>Stock Level</th>
          </tr>
        </thead>
        <tbody>
          <?php
          // Get Inventory Levels
          $sql_items = "SELECT item_name, stock FROM items";
          $result_items = $conn->query($sql_items);

          if ($result_items->num_rows > 0) {
            while ($row = $result_items->fetch_assoc()) {
              echo "<tr><td>" . htmlspecialchars($row['item_name']) . "</td><td>" . $row['stock'] . "</td></tr>";
            }
          } else {
            echo "<tr><td colspan='2'>No data available</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>

    <!-- User Activity Report -->
    <div class="report">
      <h3>User Activity</h3>
      <table>
        <thead>
          <tr>
            <th>Username</th>
            <th>Login Date</th>
          </tr>
        </thead>
        <tbody>
          <?php
          // Get User Activity (e.g., last login)
          $sql_user_activity = "SELECT username, MAX(created_at) AS last_login FROM users GROUP BY username";
          $result_user_activity = $conn->query($sql_user_activity);

          if ($result_user_activity->num_rows > 0) {
            while ($row = $result_user_activity->fetch_assoc()) {
              echo "<tr><td>" . htmlspecialchars($row['username']) . "</td><td>" . $row['last_login'] . "</td></tr>";
            }
          } else {
            echo "<tr><td colspan='2'>No data available</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>

  </div>
</body>
</html>
