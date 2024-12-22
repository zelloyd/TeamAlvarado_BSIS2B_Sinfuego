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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login'])) {
        // Login Process
        $username_or_email = $_POST['username_or_email'];
        $password = $_POST['password']; // Plaintext password submitted by the user

        // Query to check if the username or email exists
        $login_query = "SELECT * FROM users WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($login_query);
        $stmt->bind_param("ss", $username_or_email, $username_or_email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Directly compare plaintext passwords (only for controlled environments)
            if ($password === $user['password']) {
                // Start session
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];

                header("Location: homepageclient.php"); // Redirect to homepage after successful login
                exit;
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "No user found with that username or email.";
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
    <title>Login</title>
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
            <h3>Login to Your Account</h3>

            <?php if ($error): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="">

                <label for="username_or_email">Username or Email</label>
                <input type="text" name="username_or_email" id="username_or_email" placeholder="Enter your username or email" required>

                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Enter your password" required>

                <button type="submit" name="login">Login</button>
            </form>

            <div class="text-center">
                <p>Don't have an account? <a href="registration.php" style="color: #009688;">Register</a></p>
            </div>
        </div>
    </div>

</body>
</html>
