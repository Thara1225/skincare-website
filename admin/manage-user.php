<?php
session_start();
require_once 'dbconnection.php'; // Include database connection

// Check if the user is an admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: index.php"); // Redirect to login if not an admin
    exit();
}

// Query to fetch all users from the database
$query = "SELECT user_id, name, email FROM users";
$result = $conn->query($query);

if (!$result) {
    // Handle error if the query fails
    die("Error fetching users: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Cinnamon Skin Care</title>
    <link rel="stylesheet" href="manage-user.css">
</head>
<body>
    <div class="manage-container">
        <h1>Manage Users</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $user['user_id'] ?></td>
                    <td><?= $user['name'] ?></td>
                    <td><?= $user['email'] ?></td>
                    <td>
                        <a href="edit_user.php?id=<?= $user['user_id'] ?>">Edit</a>
                        <a href="delete_user.php?id=<?= $user['user_id'] ?>">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <div style="text-align: center; margin-top: 20px;">
            <button onclick="window.location.href='dashboard.php'" class="dashboard-btn">Go to Admin Dashboard</button>
        </div>
    </div>
    
</body>
</html>
