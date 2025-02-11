<?php
session_start();
require_once 'dbconnection.php';

// Get user ID (logged-in user or guest session)
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : session_id();

// Get the order_id from the URL
if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    // Fetch order details from orders table
    $sql = "SELECT * FROM orders WHERE order_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $order_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the order exists for the user
    if ($result->num_rows > 0) {
        $order = $result->fetch_assoc();

        // Fetch the order items from order_items table
        $sql_items = "SELECT order_items.*, products.name, products.photo 
                      FROM order_items 
                      JOIN products ON order_items.product_id = products.product_id 
                      WHERE order_items.order_id = ?";
        $stmt_items = $conn->prepare($sql_items);
        $stmt_items->bind_param("i", $order_id);
        $stmt_items->execute();
        $items_result = $stmt_items->get_result();
        $order_items = $items_result->fetch_all(MYSQLI_ASSOC);
    } else {
        echo "<p>Order not found or you do not have permission to view it.</p>";
        exit();
    }
} else {
    echo "<p>No order selected.</p>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - Cinnamon Skin Care</title>
    <link rel="stylesheet" href="checkout.css">
</head>
<body>
<header>
        <div class="logo">
            <img src="imgs/logo.jpg" alt="Cinnamon Skin Care Logo">
            <h1>Order Details</h1>
        </div>
        <nav>
            <li><a href="account.php">Account</a></li>
        </nav>
    </header>

    <section id="order-summary">
        <h2>Order Summary</h2>
        <p><strong>Order ID:</strong> <?= $order['order_id'] ?></p>
        <p><strong>Date:</strong> <?= $order['order_date'] ?></p>
        <p><strong>First Name:</strong> <?= $order['first_name'] ?></p>
        <p><strong>Last Name:</strong> <?= $order['last_name'] ?></p>
        <p><strong>Address:</strong> <?= $order['address'] ?></p>
        <p><strong>City:</strong> <?= $order['city'] ?></p>
        <p><strong>Postal Code:</strong> <?= $order['postal_code'] ?></p>
        <p><strong>Phone:</strong> <?= $order['phone'] ?></p>
        <p><strong>Country:</strong> <?= $order['country'] ?></p>
        <p><strong>Payment Method:</strong> <?= ucfirst($order['payment_method']) ?></p>
        <p><strong>Subtotal:</strong> LKR <?= number_format($order['subtotal'], 2) ?></p>
        <p><strong>Shipping:</strong> LKR <?= number_format($order['shipping'], 2) ?></p>
        <p><strong>Total:</strong> LKR <?= number_format($order['total'], 2) ?></p>
    </section>

    <section id="order-items">
        <h2>Order Items</h2>
        <ul>
            <?php foreach ($order_items as $item): ?>
                <li>
                    <img src="<?= $item['photo'] ?>" alt="<?= $item['name'] ?>" width="100">
                    <p><strong><?= $item['name'] ?></strong></p>
                    <p>Quantity: <?= $item['quantity'] ?></p>
                    <p>Price: LKR <?= number_format($item['price'], 2) ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>

    <a href="account.php">Back to Account</a>
</body>
</html>