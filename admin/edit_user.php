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

$id = $_GET['id'];

// Fetch the user data from the database
$query = "SELECT user_id, name, email FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    // If no user is found, redirect back to the user management page
    header("Location: manage-user.php");
    exit();
}

// Fetch the user data
$stmt->bind_result($id, $name, $email);
$stmt->fetch();

// Handle form submission (update user data)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_name = trim($_POST['name']);
    $new_email = trim($_POST['email']);

    // Update the user data
    $update_query = "UPDATE users SET name = ?, email = ? WHERE user_id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("ssi", $new_name, $new_email, $id);

    if ($update_stmt->execute()) {
        $_SESSION['success_message'] = "User details updated successfully!";
        header("Location: manage-user.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Failed to update user details.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Cinnamon Skin Care</title>
    <link rel="stylesheet" href="edit_user.css">
</head>
<body>
    <div class="manage-container">
        <h1>Edit User</h1>
        
        <!-- Display messages -->
        <?php
        if (isset($_SESSION['error_message'])) {
            echo '<p style="color: red;">' . $_SESSION['error_message'] . '</p>';
            unset($_SESSION['error_message']);
        }
        if (isset($_SESSION['success_message'])) {
            echo '<p style="color: green;">' . $_SESSION['success_message'] . '</p>';
            unset($_SESSION['success_message']);
        }
        ?>
        
        <form action="edit_user.php?id=<?= $id ?>" method="POST">

            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>" required>

            <label for="email">Email Address:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>

            <button type="submit">Update</button>
        </form>
    </div>
</body>
</html>
