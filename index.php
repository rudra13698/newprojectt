<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index Page</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: url('/New%20project/img/img.webp') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            text-align: center;
            background: rgba(255, 255, 255, 0.8);
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            max-width: 500px;
            width: 100%;
        }
        h1 {
            font-size: 2rem;
            margin-bottom: 1.5rem;
            color: #333;
        }
        .button-group {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        a {
            text-decoration: none;
            color: white;
        }
        .button {
            display: inline-block;
            padding: 0.75rem;
            font-size: 1rem;
            background: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-align: center;
        }
        .button:hover {
            background: #0056b3;
        }
        .button:focus {
            outline: 2px solid #0056b3;
            outline-offset: 2px;
        }
        .hint {
            margin-top: 1rem;
            font-size: 0.875rem;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Online Store</h1>
        <div class="button-group">
            <a href="home.php" class="button">Home</a>
            <a href="about.html" class="button">About Us</a>
            <a href="contact.html" class="button">Contact Us</a>
            <a href="/New%20project/user/login.php" class="button">User Login</a>
            <a href="/New%20project/admin/login.php" class="button">Admin Login</a>
        </div>
        <p class="hint">Choose an option to proceed.</p>
    </div>
</body>
</html>
