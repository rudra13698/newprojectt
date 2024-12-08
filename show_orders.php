<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "website";

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch orders for the logged-in user
$username = $_SESSION['username'];
$sql = "SELECT * FROM orders WHERE username = ? ORDER BY order_date DESC";

// Prepare the statement
if (!$stmt = $conn->prepare($sql)) {
    die("SQL preparation error: " . $conn->error);
}

// Bind parameters and execute
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
</head>
<body>
    <h1>Your Orders</h1>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Payment Method</th>
                <th>Order Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['product_name']) ?></td>
                        <td><?= htmlspecialchars($row['quantity']) ?></td>
                        <td>₹<?= htmlspecialchars($row['price']) ?></td>
                        <td><?= htmlspecialchars($row['payment_method']) ?></td>
                        <td><?= htmlspecialchars($row['order_date']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No orders found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>

<!-- Thank You Message Section -->
<?php
// Use the already started session
if (isset($_SESSION['checkout_message'])) {
    $message = $_SESSION['checkout_message'];
    unset($_SESSION['checkout_message']);
} else {
    $message = "Your order could not be processed.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You</title>
</head>
<body>

</body>
</html>
