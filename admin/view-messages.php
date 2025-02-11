<?php
// Database connection
$conn = mysqli_connect("localhost", "root", "", "cinnamon_db");

// Fetch messages
$sql = "SELECT * FROM messages ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - View Messages</title>
    <link rel="stylesheet" href="view-messages.css">
</head>
<body>
    <header>
        <h1>Messages from Users</h1>
    </header>
    <main>
       
        <?php
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<div class='message'>";
                echo "<p><strong>Name:</strong> " . htmlspecialchars($row['name']) . "</p>";
                echo "<p><strong>Email:</strong> " . htmlspecialchars($row['email']) . "</p>";
                echo "<p><strong>Message:</strong> " . nl2br(htmlspecialchars($row['message'])) . "</p>";
                echo "<p><em>Received on: " . $row['created_at'] . "</em></p>";
                echo "</div><hr>";
            }
        } else {
            echo "<p>No messages found.</p>";
        }
        ?>
        <div style="text-align: center; margin-top: 20px;">
            <button onclick="window.location.href='dashboard.php'" class="dashboard-btn">Go to Admin Dashboard</button>
        </div>
    </main>
    <footer>
        <p>&copy; 2024 Cinnamon Skin Care. All rights reserved.</p>
    </footer>
</body>
</html>