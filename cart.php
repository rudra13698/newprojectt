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

$session_id = session_id();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && isset($_POST['cart_id'])) {
        $action = $_POST['action'];
        $cart_id = (int)$_POST['cart_id'];
        
        if ($action === 'increase') {
            $conn->query("UPDATE cart SET quantity = quantity + 1 WHERE id = $cart_id");
        } elseif ($action === 'decrease') {
            $conn->query("UPDATE cart SET quantity = GREATEST(quantity - 1, 1) WHERE id = $cart_id");
        } elseif ($action === 'delete') {
            $conn->query("DELETE FROM cart WHERE id = $cart_id");
        }
    }
}

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
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
        }
        header, main {
            max-width: 1200px;
            margin: auto;
            padding: 10px;
        }
        header {
            background-color: #fff;
            border-bottom: 1px solid #ddd;
            padding: 10px;
        }
        .cart-items {
            margin: 20px 0;
        }
        .cart-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }
        .cart-item img {
            width: 80px;
            margin-right: 20px;
        }
        .cart-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .cart-total {
            text-align: right;
            margin-top: 20px;
            font-size: 1.2rem;
        }
        .checkout-btn {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            text-align: center;
        }
        .checkout-btn:hover {
            background-color: #0056b3;
        }
        button {
            padding: 5px 10px;
            cursor: pointer;
        }
    </style>
    <script>
        function updateCart(action, cartId) {
            const formData = new FormData();
            formData.append('action', action);
            formData.append('cart_id', cartId);

            fetch('', {
                method: 'POST',
                body: formData
            }).then(() => {
                window.location.reload();
            });
        }
    </script>
</head>
<body>
    <header>
        <h1>Shopping Cart</h1>
    </header>
    <main>
        <div class="cart-items">
            <?php if (!empty($cart_items)): ?>
                <?php foreach ($cart_items as $item): ?>
                    <div class="cart-item">
                        <img src="img/chip.jpg" alt="<?= htmlspecialchars($item['product_name']) ?>">
                        <div>
                            <h3><?= htmlspecialchars($item['product_name']) ?></h3>
                            <p>Price: ₹<?= htmlspecialchars($item['price']) ?></p>
                            <p>Quantity: <?= htmlspecialchars($item['quantity']) ?></p>
                        </div>
                        <div class="cart-controls">
                            <button onclick="updateCart('increase', <?= $item['id'] ?>)">+</button>
                            <button onclick="updateCart('decrease', <?= $item['id'] ?>)">-</button>
                            <button onclick="updateCart('delete', <?= $item['id'] ?>)">Delete</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Your cart is empty.</p>
            <?php endif; ?>
        </div>
        <div class="cart-total">
            <strong>Total: ₹<?= $total_price ?></strong>
        </div>
        <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
        
    </main>
</body>
</html>

<div class="cart-total">
    <strong>Total: ₹<?= $total_price ?></strong>
</div>

