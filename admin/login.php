<?php
// Start session
session_start();

// Database connection
$servername = "localhost";  // Your database server
$username = "root";         // Your database username
$password = "";             // Your database password
$dbname = "website";        // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Define variables and initialize with empty values
$admin_username = $admin_password = "";
$error_message = "";

// Process form when submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the submitted username and password
    $admin_username = $_POST['admin-username'];
    $admin_password = $_POST['admin-password'];

    // SQL query to fetch the admin data from the database
    $sql = "SELECT * FROM admins WHERE username = ?";
    if ($stmt = $conn->prepare($sql)) {
        // Bind the username parameter to the query
        $stmt->bind_param("s", $admin_username);
        
        // Execute the query
        $stmt->execute();
        
        // Get the result
        $result = $stmt->get_result();
        
        // Check if the admin exists
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($admin_password, $row['password'])) {
                // Set session variables for the logged-in admin
                $_SESSION['admin_id'] = $row['id'];
                $_SESSION['admin_username'] = $row['username'];

                // Redirect to the admin dashboard
                header("Location: dashboard.php");
                exit();
            } else {
                // Invalid password
                $error_message = "Invalid credentials.";
            }
        } else {
            // Invalid username
            $error_message = "Invalid credentials.";
        }

        // Close statement
        $stmt->close();
    } else {
        $error_message = "Something went wrong. Please try again.";
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
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
            background: #FF4500; /* Distinct color for admin login */
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
        }
        button:focus {
            outline: 2px solid #FF6347;
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
    <div class="login-container">
        <h1>Admin Login</h1>
        <form action="login.php" method="post" aria-labelledby="adminLoginForm">
            <label for="admin-username">Admin Username</label>
            <input type="text" id="admin-username" name="admin-username" aria-required="true" placeholder="Enter admin username" required>
            
            <label for="admin-password">Password</label>
            <input type="password" id="admin-password" name="admin-password" aria-required="true" placeholder="Enter admin password" required>

            <button type="submit">Login</button>
        </form>
        <?php
        // Display error message if login fails
        if (!empty($error_message)) {
            echo "<p style='color: red; text-align: center;'>$error_message</p>";
        }
        ?>
        <p class="hint">Not an admin? <a href="../user/login.html">Go to User Login</a>.</p>
    </div>
</body>
</html>
