<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "cinnamon_db");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if a product ID is provided
if (!isset($_GET['product_id']) || empty($_GET['product_id'])) {
    die("Invalid product ID.");
}

$product_id = intval($_GET['product_id']); // Correct parameter name

// Fetch the selected product from the database
$sql = "SELECT product_id, name, price, description, how_to_use, photo FROM products WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if the product exists
if ($result->num_rows === 0) {
    die("Product not found.");
}

$product = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - Cinnamon Skin Care</title>
    <link rel="stylesheet" href="productdetails.css">
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
                    <li><a href="logout.php">Logout</a></li>
                    <li><a href="cart.php">Cart</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                <?php endif; ?>
        </ul>
    </nav>
</header>

<main>
    <section class="product-detail">
        <div class="product-info">
            <img src="<?php echo htmlspecialchars($product['photo']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
            <div class="details">
                <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                <p><?php echo htmlspecialchars($product['description']); ?></p>
                <h2>How to Use:</h2>
                <p><?php echo htmlspecialchars($product['how_to_use']); ?></p>
                <h2>Price: LKR <?php echo number_format($product['price'], 2); ?></h2>
                <button class="add-to-cart" onclick="addToCart(<?php echo $product['product_id']; ?>)">Add to Cart</button>
            </div>
        </div>
    </section>
</main>

<footer>
    <p>&copy; 2024 Cinnamon Skin Care Products. All rights reserved.</p>
</footer>

<script>
    function addToCart(productId) {
        window.location.href = "add_to_cart.php?product_id=" + productId; // Updated parameter name
    }
</script>

</body>
</html>

<?php
// Close database connection
$stmt->close();
$conn->close();
?>