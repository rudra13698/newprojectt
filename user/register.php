<?php
// Start session
session_start();

// Database configuration
$servername = "localhost";
$username = "root";  // Your MySQL username
$password = "";      // Your MySQL password
$dbname = "website";  // Your database name

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = trim($_POST['username']);
    $email = trim($_POST['email']);
    $pass = trim($_POST['password']);
    $confirm_pass = trim($_POST['confirm-password']);

    // Validate form fields
    if (empty($user) || empty($email) || empty($pass) || empty($confirm_pass)) {
        $error = "All fields are required.";
    } elseif ($pass !== $confirm_pass) {
        $error = "Passwords do not match.";
    } else {
        // Hash the password
        $hashed_password = password_hash($pass, PASSWORD_DEFAULT);

        // Check if the username or email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        if ($stmt === false) {
            die('MySQL prepare error: ' . $conn->error);
        }

        $stmt->bind_param("ss", $user, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Username or Email already taken.";
        } else {
            // Insert new user into the database
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            if ($stmt === false) {
                die('MySQL prepare error: ' . $conn->error);
            }

            $stmt->bind_param("sss", $user, $email, $hashed_password);
            if ($stmt->execute()) {
                $_SESSION['username'] = $user;
                header("Location: login.php");  // Redirect to login page
                exit();
            } else {
                $error = "Error registering user. Please try again.";
            }
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
            background: url('../img/img.webp') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .register-container {
            max-width: 600px;
            width: 90%;
            background: rgba(255, 255, 255, 0.9);
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
        input[type="text"], input[type="password"], input[type="email"] {
            width: 100%;
            padding: 0.75rem;
            margin-bottom: 1rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
        }
        input[type="text"]:focus, input[type="password"]:focus, input[type="email"]:focus {
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
    </style>
</head>
<body>
    <div class="register-container">
        <h1>Register</h1>
        <?php if (!empty($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="register.php" method="post" aria-labelledby="registerForm">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" aria-required="true" placeholder="Enter your username" required>
            
            <label for="email">Email</label>
            <input type="email" id="email" name="email" aria-required="true" placeholder="Enter your email" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" aria-required="true" placeholder="Enter your password" required>

            <label for="confirm-password">Confirm Password</label>
            <input type="password" id="confirm-password" name="confirm-password" aria-required="true" placeholder="Confirm your password" required>

            <button type="submit">Register</button>
        </form>
        <p class="hint">Already have an account? <a href="login.html">Login here</a>.</p>
    </div>
</body>
</html>
