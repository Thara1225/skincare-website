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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $description = trim($_POST['description']);
    
    // Image upload handling
    $targetDir = "imgs/";  // Change to your actual image folder
    $photoName = basename($_FILES["photo"]["name"]);
    $targetFilePath = $targetDir . $photoName;
    $uploadOk = 1;
    
    // Check file type
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
    $allowedTypes = array("jpg", "jpeg", "png", "gif");
    if (!in_array($fileType, $allowedTypes)) {
        $error = "Only JPG, JPEG, PNG, and GIF files are allowed.";
        $uploadOk = 0;
    }

    if ($uploadOk && move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFilePath)) {
        // Insert product into database with image path
        $stmt = $conn->prepare("INSERT INTO products (name, price, description, photo) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sdss", $name, $price, $description, $targetFilePath);
        
        if ($stmt->execute()) {
            $success = "Product added successfully!";
        } else {
            $error = "Error adding product: " . mysqli_error($conn);
        }
        $stmt->close();
    } else {
        $error = "Error uploading image.";
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Cinnamon Skin Care</title>
    <link rel="stylesheet" href="add-products.css">
</head>
<body>
    <div class="manage-container">
        <h1>Add New Product</h1>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>

        <form action="add-products.php" method="POST" enctype="multipart/form-data">
            <label for="name">Product Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="price">Price (LKR):</label>
            <input type="number" id="price" name="price" step="0.01" required>

            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="5" required></textarea>

            <label for="photo">Product Photo:</label>
            <input type="file" id="photo" name="photo" accept="image/*" required>

            <button type="submit">Add Product</button>
        </form>
        <a href="manage-products.php" class="back-button">Back to Products</a>
    </div>
</body>
</html>


