<?php
// Start session
session_start();

// Database configuration
$servername = "localhost";
$username = "root"; // Replace with your database username
$password = "";     // Replace with your database password
$dbname = "website";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);  // Logging can be added here
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = trim($_POST['username']);
    $pass = trim($_POST['password']);

    // Input validation
    if (empty($user) || empty($pass)) {
        $error = "Please fill in both fields.";
    } else {
        // Prepare and execute query
        $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
        $stmt->bind_param("s", $user);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($hashed_password);
            $stmt->fetch();

            // Verify password
            if (password_verify($pass, $hashed_password)) {
                // Successful login
                $_SESSION['username'] = $user;
                header("Location: dashboard.php"); // Redirect to a new page
                exit();
            } else {
                $error = "Invalid credentials.";
            }
        } else {
            $error = "Invalid credentials.";
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
</head>
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background: url('../img/img.webp') no-repeat center center fixed;
        background-size: cover;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }
    .login-container {
        max-width: 600px;
        width: 90%;
        background: rgba(255, 255, 255, 0.9); /* Semi-transparent white background */
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        padding: 2rem;
        text-align: left;
    }
    h1 {
        font-size: 1.5rem;
        margin-bottom: 1rem;
        text-align: center;
        color: #333;
    }
    label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: bold;
        color: #555;
    }
    input[type="text"], input[type="password"] {
        width: 100%;
        padding: 0.75rem;
        margin-bottom: 1rem;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 1rem;
    }
    input[type="text"]:focus, input[type="password"]:focus {
        border-color: #007BFF;
        outline: none;
    }
    button {
        width: 100%;
        padding: 0.75rem;
        background: #007BFF;
        color: white;
        border: none;
        border-radius: 4px;
        font-size: 1rem;
        cursor: pointer;
    }
    button:focus {
        outline: 2px solid #0056b3;
        outline-offset: 2px;
    }
    button:disabled {
        background: #ccc;
        cursor: not-allowed;
    }
    .hint {
        font-size: 0.875rem;
        color: #666;
        text-align: center;
        margin-top: 1rem;
    }
    .hint a {
        color: #007BFF;
        text-decoration: none;
    }
    .hint a:hover {
        text-decoration: underline;
    }
    .register-btn {
        width: 100%;
        padding: 0.75rem;
        background: #28a745;  /* Green background */
        color: white;
        border: none;
        border-radius: 4px;
        font-size: 1rem;
        cursor: pointer;
        margin-top: 1rem;
    }
    .register-btn:hover {
        background-color: #218838;  /* Darker green on hover */
    }
</style>
<body>
    <div class="login-container">
        <h1>Login</h1>
        <?php if (!empty($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="login.php" method="post">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>
        </form>
        
        <a href="register.php">
            <button class="register-btn">Register</button>
        </a>
    </div>
</body>
</html>
