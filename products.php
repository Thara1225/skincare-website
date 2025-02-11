<?php
session_start();
require_once 'dbconnection.php';

// Fetch products from the database
$sql = "SELECT product_id, name AS product_name, price AS product_price, description AS product_description, photo AS product_photo FROM products";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Products - Cinnamon Skin Care</title>
    <link rel="stylesheet" href="products.css">
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
            <li><a href="contact.php">Contact Us</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="account.php">My Account</a></li>
                <li><a href="cart.php">Cart</a></li>
                <li><a href="logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php">Login</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<main>
    <h2 class="section-title">Our Products</h2>
    <div class="product-container">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="product">';
                echo '<a href="productdetails.php?product_id=' . $row['product_id'] . '">';

                // Display the correct product image or a default image if missing
                $imagePath = !empty($row['product_photo']) ? htmlspecialchars($row['product_photo']) : 'imgs/default.jpg';
                echo '<img src="' . $imagePath . '" alt="' . htmlspecialchars($row['product_name']) . '" width="200px">';

                echo '</a>';
                echo '<h3><a href="productdetails.php?product_id=' . $row['product_id'] . '">' . htmlspecialchars($row['product_name']) . '</a></h3>';
                echo '<p>LKR ' . number_format($row['product_price'], 2) . '</p>';
                echo '</div>';
            }
        } else {
            echo "<p>No products available.</p>";
        }
        ?>
    </div>
</main>

<footer>
    <p>&copy; 2024 Cinnamon Skin Care. All rights reserved.</p>
</footer>

</body>
</html>

<?php
// Close database connection
$conn->close();
?>