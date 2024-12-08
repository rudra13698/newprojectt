<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Redirect to home.php if logged in
header("Location: ../home.php");
exit();
?>
