<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection details
$host = 'localhost';
$db = 'zaikicks';
$user = 'root';
$pass = '';

// Connect to the database
$conn = new mysqli($host, $user, $pass, $db);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['register'])) {
        // Registration Process
        $email = $_POST['email'];
        $username = $_POST['username'];
        $password = $_POST['password']; // Plaintext password

        // Insert user data into the users table without hashing the password
        $sql = "INSERT INTO users (email, username, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $email, $username, $password);

        if ($stmt->execute()) {
            $message = "Registration successful! You can now log in.";
            header("Location: login.php"); // Redirect to login page after successful registration
            exit; // Ensure no further processing
        } else {
            $error = "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #009688;
        }

        .container {
            display: flex;
            max-width: 600px;
            margin: 50px auto;
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .form-container {
            width: 100%;
            padding: 20px;
        }

        .form-container h3 {
            margin-bottom: 20px;
            font-size: 1.3em;
            color: #444;
        }

        .form-container form {
            display: flex;
            flex-direction: column;
        }

        .form-container form label {
            margin: 10px 0 5px;
            font-size: 0.9em;
            color: #333;
        }

        .form-container form input {
            padding: 10px;
            font-size: 0.9em;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form-container form button {
            margin-top: 20px;
            padding: 10px;
            background-color: #009688;
            color: white;
            font-size: 1em;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .form-container form button:hover {
            background-color: #00796b;
        }

        .form-container .text-center {
            text-align: center;
            margin-top: 20px;
        }

        .form-container .error {
            color: red;
        }

        .form-container .message {
            color: green;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="form-container">
            <h3>Register a New Account</h3>

            <?php if ($error): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if ($message): ?>
                <div class="message"><?php echo $message; ?></div>
            <?php endif; ?>

            <form method="POST" action="">

                <label for="email">Email</label>
                <input type="email" name="email" id="email" placeholder="Enter your email" required>

                <label for="username">Username</label>
                <input type="text" name="username" id="username" placeholder="Enter your username" required>

                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Enter your password" required>

                <button type="submit" name="register">Register</button>
            </form>

            <div class="text-center">
                <p>Already have an account? <a href="login.php" style="color: #009688;">Login</a></p>
            </div>
        </div>
    </div>

</body>
</html>
