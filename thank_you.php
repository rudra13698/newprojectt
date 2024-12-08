<?php
session_start();

// Clear the cart after checkout
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "website";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Clear the cart after successful checkout
$session_id = session_id();
$conn->query("DELETE FROM cart WHERE session_id = '$session_id'");

$conn->close();

// Set a thank-you message to display
$message = "Your order has been placed successfully.";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 20px;
        }
        .thank-you-message {
            margin-top: 50px;
        }
        .return-home {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .return-home:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>Thank You for Your Order!</h1>
    <div class="thank-you-message">
        <p><?= htmlspecialchars($message) ?></p>
    </div>
    <a href="show_orders.php">View Your Orders</a>
    <a href="index.php" class="return-home">Return to Home</a>
</body>
</html>
