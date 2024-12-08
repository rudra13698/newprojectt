<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "website";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get session ID (cart session)
$session_id = session_id();
$sql = "SELECT * FROM cart WHERE session_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $session_id);
$stmt->execute();
$result = $stmt->get_result();

$total_price = 0;
$cart_items = [];

while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $total_price += $row['price'] * $row['quantity'];
}

$stmt->close();

// Handle form submission (simulate checkout process)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user input
    $customer_name = htmlspecialchars($_POST['customer_name']);
    $customer_email = htmlspecialchars($_POST['customer_email']);
    $customer_address = htmlspecialchars($_POST['customer_address']);
    $payment_method = htmlspecialchars($_POST['payment_method']);

    // Get the logged-in username from the session
    $username = $_SESSION['username'];

    // Save order details to the 'orders' table
    $insert_order = $conn->prepare("
        INSERT INTO orders (username, customer_name, customer_email, customer_address, payment_method, product_name, quantity, price, order_date)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");

    // Insert each item in the cart as a separate order
    foreach ($cart_items as $item) {
        $insert_order->bind_param(
            "ssssssid", // 's' for string, 'i' for integer
            $username, // Add the username here
            $customer_name,
            $customer_email,
            $customer_address,
            $payment_method,
            $item['product_name'],
            $item['quantity'],
            $item['price']
        );
        $insert_order->execute();
    }
    $insert_order->close();

    // Clear the cart after successful checkout
    $conn->query("DELETE FROM cart WHERE session_id = '$session_id'");

    // Redirect to the Thank You page
    $message = "Thank you, $customer_name! Your order has been placed.";
    $_SESSION['checkout_message'] = $message;
    header("Location: thank_you.php");
    exit;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; }
        header, main { max-width: 1200px; margin: auto; padding: 10px; }
        header { background-color: #fff; border-bottom: 1px solid #ddd; padding: 10px; }
        .checkout-form { margin: 20px 0; }
        .cart-summary { margin: 20px 0; }
        .cart-item { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #ddd; }
        input, select, textarea, button { width: 100%; margin: 10px 0; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        button { background-color: #007BFF; color: white; border: none; cursor: pointer; }
        button:hover { background-color: #0056b3; }
    </style>
</head>
<body>
    <header>
        <h1>Checkout</h1>
    </header>
    <main>
        <h2>Order Summary</h2>
        <div class="cart-summary">
            <?php if (!empty($cart_items)): ?>
                <?php foreach ($cart_items as $item): ?>
                    <div class="cart-item">
                        <div>
                            <h3><?= htmlspecialchars($item['product_name']) ?></h3>
                            <p>Quantity: <?= htmlspecialchars($item['quantity']) ?></p>
                        </div>
                        <div>₹<?= htmlspecialchars($item['price'] * $item['quantity']) ?></div>
                    </div>
                <?php endforeach; ?>
                <div class="cart-item">
                    <strong>Total</strong>
                    <strong>₹<?= $total_price ?></strong>
                </div>
            <?php else: ?>
                <p>Your cart is empty.</p>
            <?php endif; ?>
        </div>

        <h2>Billing Information</h2>
        <form method="POST" class="checkout-form">
            <label for="customer_name">Full Name</label>
            <input type="text" id="customer_name" name="customer_name" required>

            <label for="customer_email">Email Address</label>
            <input type="email" id="customer_email" name="customer_email" required>

            <label for="customer_address">Shipping Address</label>
            <textarea id="customer_address" name="customer_address" rows="4" required></textarea>

            <label for="payment_method">Payment Method</label>
            <select id="payment_method" name="payment_method" required>
                <option value="credit_card">Credit Card</option>
                <option value="debit_card">Debit Card</option>
                <option value="cash_on_delivery">Cash on Delivery</option>
            </select>

            <button type="submit">Place Order</button>
        </form>
    </main>
</body>
</html>
