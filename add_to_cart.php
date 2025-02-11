<?php
session_start();
require_once 'dbconnection.php';

// Ensure the product_id is provided
if (!isset($_GET['product_id']) || empty($_GET['product_id'])) {
    header("Location: products.php");
    exit();
}

// Get product ID and sanitize it
$product_id = intval($_GET['product_id']);

// Ensure user_id is set
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 99999; // Assign guest user ID
}
$user_id = $_SESSION['user_id'];

// Check if the user exists in the database
$user_check = $conn->prepare("SELECT user_id FROM users WHERE user_id = ?");
$user_check->bind_param("i", $user_id);
$user_check->execute();
$result = $user_check->get_result();

if ($result->num_rows === 0) {
    die("Error: User does not exist. Please log in.");
}

// Fetch product details
$sql = "SELECT price, name FROM products WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Product not found.");
}

$product = $result->fetch_assoc();
$price = $product['price'];
$product_name = $product['name'];

// Add product to cart
$sql = "INSERT INTO cart (user_id, product_id, quantity, price)
        VALUES (?, ?, 1, ?)
        ON DUPLICATE KEY UPDATE quantity = quantity + 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $user_id, $product_id, $price);
$stmt->execute();

// Redirect to cart page
header("Location: cart.php");
exit();
?>
