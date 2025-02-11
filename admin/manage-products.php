<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

// Database connection
$host = "localhost"; // Change if needed
$user = "root"; // Your database username
$password = ""; // Your database password
$database = "cinnamon_db"; // Your database name

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch products from the database
$sql = "SELECT product_id, name, price FROM products";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Cinnamon Skin Care</title>
    <link rel="stylesheet" href="manage-product.css">
</head>
<body>
    <div class="manage-container">
        <h1>Manage Products</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($product = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($product['product_id']) ?></td>
                            <td><?= htmlspecialchars($product['name']) ?></td>
                            <td><?= htmlspecialchars($product['price']) ?> LKR</td>
                            <td>
                                <a href="edit_product.php?id=<?= $product['product_id'] ?>" class="edit-button">Edit</a>
                                <a href="delete_product.php?id=<?= $product['product_id'] ?>" class="delete-button" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No products found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <a href="add-products.php" class="add-button">Add New Product</a>
    </div>
</body>
</html>
<?php $conn->close(); ?>
