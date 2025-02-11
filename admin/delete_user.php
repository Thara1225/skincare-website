<?php
session_start();
require_once 'dbconnection.php'; // Include database connection

// Check if the user is an admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: index.php"); // Redirect to login if not an admin
    exit();
}

// Check if 'id' parameter is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: manage-user.php"); // Redirect back to the user management page if no valid id is provided
    exit();
}

$user_id = $_GET['id'];

// Delete the user from the database
$delete_query = "DELETE FROM users WHERE user_id = ?";
$stmt = $conn->prepare($delete_query);
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    $_SESSION['success_message'] = "User deleted successfully!";
} else {
    $_SESSION['error_message'] = "Failed to delete user.";
}

// Redirect back to the user management page
header("Location: manage-user.php");
exit();
?>
