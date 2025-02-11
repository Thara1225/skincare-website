<?php
session_start();
// Remove the following line to prevent session data from displaying
// var_dump($_SESSION); 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "cinnamon_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$sql = "SELECT name, email FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Fetch most recent order
$order_sql = "SELECT order_id, order_date FROM orders WHERE user_id = ? ORDER BY order_date DESC LIMIT 1";
$order_stmt = $conn->prepare($order_sql);
$order_stmt->bind_param("i", $user_id);
$order_stmt->execute();
$order_result = $order_stmt->get_result();
$order = $order_result->fetch_assoc();
$order_stmt->close();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    
    if (!empty($name) && !empty($email)) {
        $update_sql = "UPDATE users SET name = ?, email = ? WHERE user_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssi", $name, $email, $user_id);
        
        if ($update_stmt->execute()) {
            $success = "Profile updated successfully!";
        } else {
            $error = "Error updating profile.";
        }
        $update_stmt->close();
    } else {
        $error = "All fields are required.";
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account - Cinnamon Skin Care</title>
    <link rel="stylesheet" href="account.css">
</head>
<body>
    <header>
        <div class="logo">
            <img src="imgs/logo.jpg" alt="Cinnamon Skin Care Logo">
            <h1>Cinnamon Skin Care</h1>
        </div>
        <nav>
            <ul>
                <li><a href="homepage.php">Home</a></li>
                <li><a href="products.php">Products</a></li>
                <li><a href="cart.php">Cart</a></li>
                <li><a href="contact.php">Contact Us</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="account.php">My Account</a></li>
                    <li><a href="homepage.php">Logout</a></li>
                    
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main>
        <section class="account-section">
            <h1>Your Account</h1>
            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
            <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
            
            <div class="profile-info">
                <h2>Profile Information</h2>
                <form method="POST" action="account.php">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                    
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                    
                    <button type="submit">Update Profile</button>
                </form>
            </div>

            <?php if (isset($order['order_id'])): ?>
                <button onclick="window.location.href='order_details.php?order_id=<?= $order['order_id'] ?>'">View Your Order</button>
            <?php else: ?>
                <p>You have no orders yet.</p>
            <?php endif; ?>

            <button onclick="window.location.href='login.php'">Sign Out</button>
        </section>
    </main>
    <footer>
        <p>&copy; 2024 Cinnamon Skin Care Products. All rights reserved.</p>
    </footer>
</body>
</html>
