<?php
// login.php

// Start the session
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "zaikicks";

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin_username = $conn->real_escape_string($_POST['username']);
    $admin_password = $conn->real_escape_string($_POST['password']);

    // Query to validate admin credentials
    $sql = "SELECT * FROM admins WHERE username = '$admin_username' AND password = '$admin_password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Login successful
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $admin_username;
        header("Location: admin_page.php"); // Redirect to the dashboard
        exit;
    } else {
        // Invalid credentials
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    
    <style>
    body {
    background-image: url('background.jpg');
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center center;
    font-family: 'Helvetica Neue', Arial, sans-serif; /* Choose a suitable font */
    background-color:hsl(120, 12.50%, 96.90%); /* Background color */
    color: #333;
    display: flex;
   justify-content: center;
   align-items: center;
   height: 100vh; /* Set the height to 100% of the viewport */
    

  }
  
  .login-container {
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
    padding: 20px;
    margin: 0 auto; /* Center horizontally */
    max-width: 700px;
    min-width: 700px;
 
  }
  
  h1 {
    text-align: center;
    color: #333;
    margin-bottom: 20px;
  }
  
  .form-group {
    margin-bottom: 15px;
  }
  
  label {
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
  }
  
  input[type="text"],
  input[type="password"] {
    width: 85%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
  }
  
  button {
    background-color: #007bff; /* Button color */
    color: #fff;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
  }
  
  .error {
    color: #f00;
    margin-bottom: 10px;
  }

    </style>

</head>
<body>
    <div class="login-container">
        <h1>Admin Login</h1>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>

<?php
$conn->close();
?>
