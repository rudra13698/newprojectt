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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['product_name'], $_POST['price'], $_POST['quantity'])) {
        $product_name = $_POST['product_name'];
        $price = (float)$_POST['price'];
        $quantity = (int)$_POST['quantity'];
        $session_id = session_id();

        $stmt = $conn->prepare("INSERT INTO cart (product_name, price, quantity, session_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sdis", $product_name, $price, $quantity, $session_id);

        if ($stmt->execute()) {
            echo "Item added to cart successfully";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error: Missing product details";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Store</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        header, nav, main, footer {
            max-width: 1200px;
            margin: auto;
            padding: 0 10px;
        }
        header {
            background-color: #fff;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        nav ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: flex-end;
        }
        nav li {
            margin-left: 20px;
        }
        nav a {
            text-decoration: none;
            font-size: 1rem;
            color: #333;
            padding: 10px;
        }
        nav a:hover {
            background-color: #007BFF;
            color: white;
            border-radius: 4px;
        }
        .products {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .product {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
            border-radius: 5px;
            transition: transform 0.3s;
        }
        .product img {
            max-width: 100%;
            border-radius: 5px;
        }
        .product h3, .product p {
            margin: 10px 0;
        }
        .product:hover {
            transform: scale(1.05);
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }
        .add-to-cart {
            padding: 10px 15px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .add-to-cart:hover {
            background-color: #0056b3;
        }
    </style>
    <script>
        function addToCart(productName, price, quantity) {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onload = function() {
                if (xhr.status === 200) {
                    alert(xhr.responseText);
                } else {
                    alert("Error adding item to cart. Please try again.");
                }
            };
            xhr.send(`product_name=${encodeURIComponent(productName)}&price=${price}&quantity=${quantity}`);
        }
    </script>
</head>
<body>
    <header>
        <h1>Online Store</h1>
        <nav>
            <ul>
                <li><a href="#">Search</a></li>
                <li><a href="show_orders.php">My Orders</a></li>
                <li><a href="#">More</a></li>
                <li><a href="cart.php">Cart</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h2>Electronic Chips</h2>
        <section class="products">
            <?php
            // Dynamically generate product items
            for ($i = 1; $i <= 5; $i++) {
                echo "
                <div class='product'>
                    <img src='img/chip.jpg' alt='Product $i'>
                    <h3>Product Name $i</h3>
                    <p>₹400</p>
                    <button class='add-to-cart' onclick=\"addToCart('Product Name $i', 400, 1)\">Add to Cart</button>
                </div>";
            }
            ?>
        </section>
    </main>
</body>
</html>
