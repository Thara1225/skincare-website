<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

// Database connection 
$conn = mysqli_connect("localhost", "root", "", "cinnamon_db");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Validate and fetch product ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid product ID.");
}
$product_id = intval($_GET['id']);

// Fetch product details using a prepared statement
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

if (!$product) {
    die("Product not found.");
}

// Handle form submission securely
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $description = trim($_POST['description']);
    $how_to_use = trim($_POST['how_to_use']);

    // Update product using a prepared statement
    $update_stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, description = ?, how_to_use = ? WHERE id = ?");
    $update_stmt->bind_param("sdssi", $name, $price, $description, $how_to_use, $product_id);

    if ($update_stmt->execute()) {
        $success = "Product updated successfully!";
        
        // Refresh product details after update
        $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        $stmt->close();
    } else {
        $error = "Error updating product: " . mysqli_error($conn);
    }
    $update_stmt->close();
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Cinnamon Skin Care</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="manage-container">
        <h1>Edit Product</h1>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
        
        <form action="edit_product.php?id=<?= htmlspecialchars($product_id) ?>" method="POST">
            <label for="name">Product Name:</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>

            <label for="price">Price (LKR):</label>
            <input type="number" id="price" name="price" step="0.01" value="<?= htmlspecialchars($product['price']) ?>" required>

            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="5" required><?= htmlspecialchars($product['description']) ?></textarea>

            <label for="how_to_use">How to Use:</label>
            <textarea id="how_to_use" name="how_to_use" rows="5" required><?= htmlspecialchars($product['how_to_use']) ?></textarea>

            <button type="submit">Update Product</button>
        </form>
        
        <a href="manage-products.php" class="back-button">Back to Products</a>
    </div>
</body>
</html>
