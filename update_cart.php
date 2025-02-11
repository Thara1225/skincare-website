<?php
session_start();
require_once 'dbconnection.php';

// Check if cart ID and quantity are provided
if (!isset($_POST['cart_id']) || !isset($_POST['quantity'])) {
    header("Location: cart.php");
    exit();
}

$cart_id = intval($_POST['cart_id']);
$quantity = intval($_POST['quantity']);

if ($quantity < 1) {
    // Remove the item if quantity is set to 0
    $sql = "DELETE FROM cart WHERE cart_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $cart_id);
} else {
    // Update the quantity in the cart
    $sql = "UPDATE cart SET quantity = ? WHERE cart_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $quantity, $cart_id);
}

$stmt->execute();

// Redirect back to cart
header("Location: cart.php");
exit();
?>
