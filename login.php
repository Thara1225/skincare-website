<?php
session_start();
require_once 'dbconnection.php'; // Include database connection

// Function to log in user
function loginUser($email, $password, $conn) {
    $stmt = $conn->prepare("SELECT user_id, name, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $name, $stored_password, $role);
        $stmt->fetch();

        if (password_verify($password, $stored_password)) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_role'] = $role;

            return true; 
        }
    }
    return false;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    if (loginUser($email, $password, $conn)) {
        $_SESSION['success_message'] = "Login successful!";
        
        if ($_SESSION['user_role'] == 'admin') {
            header("Location: admin/dashboard.php"); 
        } else {
            header("Location: homepage.php"); 
        }
        exit();
    } else {
        $_SESSION['error_message'] = "Invalid email or password!";
        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Cinnamon Skin Care</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <header>
        <div class="logo">
            <img src="imgs/logo.jpg" alt="Cinnamon Skin Care Logo">
            <h1>Cinnamon Skin Care</h1>
        </div>
    </header>
    <main>
        <h1>Login</h1>
        <?php if (isset($_SESSION['error_message'])): ?>
            <p style="color: red;"><?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?></p>
        <?php endif; ?>
        <?php if (isset($_SESSION['success_message'])): ?>
            <p style="color: green;"><?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?></p>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <label for="email">Email Address:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>
            <button type="button" id="regButton">Register</button>
        </form>
    </main>
    <footer>
        <p>&copy; 2024 Cinnamon Skin Care. All rights reserved.</p>
    </footer>
    <script>
        document.getElementById('regButton').addEventListener('click', function() {
            window.location.href = 'registration.php'; 
        });
    </script>
</body>
</html>
