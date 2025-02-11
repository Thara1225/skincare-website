<?php
session_start();
require_once 'dbconnection.php'; // Ensure database connection

// Get user ID (logged-in user or guest session)
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : session_id();

// Fetch cart items from the database
$sql = "SELECT cart.cart_id , products.product_id, products.name, products.photo, cart.quantity, cart.price 
        FROM cart 
        JOIN products ON cart.product_id = products.product_id 
        WHERE cart.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Cinnamon Skin Care</title>
    <link rel="stylesheet" href="cart.css">
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
            <li><a href="account.php">Account</a></li>
        </ul>
    </nav>
</header>

<main>
    <section class="cart-container">
    <div style="text-align: center;">
        <h1>Your Shopping Cart</h1>
    </div>
        <?php if (empty($cart)): ?>
            <p>Your cart is empty.</p>
        <?php else: ?>
            <div class="cart-table-container" style="display: flex; justify-content: center; align-items: center; width: 100%;">
                <table style="width: 80%; max-width: 800px; margin: 0 auto; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total = 0;
                        foreach ($cart as $item):
                            $subtotal = $item['price'] * $item['quantity'];
                            $total += $subtotal;
                        ?>
                        <tr>
                            <td><img src="<?= htmlspecialchars($item['photo']) ?>" width="50" alt="<?= htmlspecialchars($item['name']) ?>"></td>
                            <td><?= htmlspecialchars($item['name']) ?></td>
                            <td>LKR <?= number_format($item['price'], 2) ?></td>
                            <td>
                                <form action="update_cart.php" method="POST">
                                    <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                                    <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" style="width: 60px;">
                                    <button type="submit">Update</button>
                                </form>
                            </td>
                            <td>LKR <?= number_format($subtotal, 2) ?></td>
                            <td>
                                <form action="remove_from_cart.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                                    <button type="submit" class="remove-btn">Remove</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div style="text-align: center;">
                <h2>Total: LKR <?= number_format($total, 2) ?></h2>
            </div>
            <form action="checkout.php" method="POST" style="text-align: center;">
              <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id) ?>">
              <button type="submit" class="checkout-btn" style="width: 200px;">Checkout</button>
            </form>

        <?php endif; ?>
    </section>
</main>

<footer>
    <p>&copy; 2024 Cinnamon Skin Care Products. All rights reserved.</p>
</footer>

</body>
</html>
