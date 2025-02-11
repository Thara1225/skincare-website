<?php
session_start();
require_once 'dbconnection.php'; // Include database connection

// Function to check if email already exists
function emailExists($email, $conn) {
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    return $stmt->num_rows > 0;
}

// Function to register user
function registerUser($name, $email, $password, $conn) {
    if (emailExists($email, $conn)) {
        return false;  // Email already exists
    }

    $role = 'user';  // Default role is 'user'
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $password, $role);
    
    return $stmt->execute();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_message'] = "Invalid email format!";
        header("Location: registration.php");
        exit();
    }

    // Check if passwords match
    if ($password !== $confirm_password) {
        $_SESSION['error_message'] = "Passwords do not match!";
        header("Location: registration.php");
        exit();
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Register user
    if (registerUser($name, $email, $hashed_password, $conn)) {
        $_SESSION['success_message'] = "Registration successful! You can now log in.";
        header("Location: login.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Error: Email is already in use.";
        header("Location: registration.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Cinnamon Skin Care</title>
    <link rel="stylesheet" href="registration.css">
</head>
<body>
    <header>
        <div class="logo">
            <img src="imgs/logo.jpg" alt="Cinnamon Skin Care Logo">
            <h1>Cinnamon Skin Care</h1>
        </div>
    </header>
    <main>
        <section class="registration-form">
            <h1>Register</h1>
            <?php if (isset($_SESSION['error_message'])): ?>
                <p style="color: red;"><?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?></p>
            <?php endif; ?>
            <?php if (isset($_SESSION['success_message'])): ?>
                <p style="color: green;"><?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?></p>
            <?php endif; ?>
            
            <form action="registration.php" method="POST">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>

                <label for="confirm-password">Confirm Password:</label>
                <input type="password" id="confirm-password" name="confirm_password" required>

                <button type="submit">Register</button>
                <button type="button" id="cancelButton">Cancel</button>
            </form>
        </section>
    </main>
    <footer>
        <p>&copy; 2024 Cinnamon Skin Care Products. All rights reserved.</p>
    </footer>
    <script>
        document.getElementById('cancelButton').addEventListener('click', function() {
            window.location.href = 'login.php'; 
        });
    </script>
</body>
</html>
