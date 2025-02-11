<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

// Database connection
$host = "localhost";
$user = "root";
$password = "";
$database = "cinnamon_db";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Validate and get the product ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid product ID.");
}
$id = intval($_GET['id']);

// Delete the product
$delete_stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
$delete_stmt->bind_param("i", $id);

if ($delete_stmt->execute()) {
    header("Location: manage-products.php?status=deleted");
    exit();
} else {
    echo "Error deleting product: " . $conn->error;
}

$delete_stmt->close();
$conn->close();
?>

