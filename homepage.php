<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cinnamon Skin Care - Home</title>
    <link rel="stylesheet" href="homepage.css">
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
        <section class="hero">
            <div class="hero-text">
                <h2>Welcome to <br>Cinnamon Skin Care</h2>
                <p>Discover the best in Ayurvedic skincare products crafted from natural ingredients for a radiant and healthy glow.</p>
                <a href="products.php" class="shop-now-btn">Shop Now</a>
            </div>
        </section>

        <section class="featured-products">
            <h2>Featured Products</h2>
            <div class="product-grid">
                <div class="product-card">
                    <img src="imgs/hp3.jpg" alt="Face Care">
                    <h3>Face Care</h3>
                    <a href="products.php" class="shop-now-btn">Shop Now</a>
                </div>

                <div class="product-card">
                    <img src="imgs/hp6.jpg" alt="Hair Care">
                    <h3>Hair Care</h3>
                    <a href="products.php" class="shop-now-btn">Shop Now</a>
                </div>

                <div class="product-card">
                    <img src="imgs/hp2.jpg" alt="Body Care">
                    <h3>Body Care</h3>
                    <a href="products.php" class="shop-now-btn">Shop Now</a>
                </div>

                <div class="product-card">
                    <img src="imgs/hp4.jpg" alt="Wellness & Herbal Remedies">
                    <h3>Wellness & Herbal Remedies</h3>
                    <a href="products.php" class="shop-now-btn">Shop Now</a>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Cinnamon Skin Care. All rights reserved.</p>
    </footer>
</body>
</html>
