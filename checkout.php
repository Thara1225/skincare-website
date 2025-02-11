
<?php
session_start();
require_once 'dbconnection.php';

// Get user ID (logged-in user or guest session)
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : session_id();

// Fetch cart items
$sql = "SELECT cart.product_id, products.name, products.photo, cart.quantity, cart.price 
        FROM cart 
        JOIN products ON cart.product_id = products.product_id 
        WHERE cart.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$cart = $result->fetch_all(MYSQLI_ASSOC);

// Calculate subtotal, shipping (0.5% per product), and total
$subtotal = 0;
$shipping = 0;
foreach ($cart as $item) {
    $subtotal += $item['price'] * $item['quantity'];
    $shipping += ($item['price'] * 0.005) * $item['quantity']; // 0.5% per product
}
$total = $subtotal + $shipping;

// Initialize variables to avoid undefined key warnings
$first_name = $last_name = $address = $city = $postal_code = $phone = $country = $payment_method = "";

// If form is submitted, process order
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : "";
    $last_name = isset($_POST['last_name']) ? trim($_POST['last_name']) : "";
    $address = isset($_POST['address']) ? trim($_POST['address']) : "";
    $city = isset($_POST['city']) ? trim($_POST['city']) : "";
    $postal_code = isset($_POST['postal_code']) ? trim($_POST['postal_code']) : "";
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : "";
    $country = isset($_POST['country']) ? trim($_POST['country']) : "";
    $payment_method = isset($_POST['payment']) ? trim($_POST['payment']) : "";

    // Validate required fields
    if (!empty($first_name) && !empty($last_name) && !empty($address) && !empty($city) && !empty($postal_code) && !empty($phone) && !empty($country) && !empty($payment_method)) {
        
        // Insert into orders table
        $insert_order = "INSERT INTO orders (user_id, first_name, last_name, address, city, postal_code, phone, country, payment_method, subtotal, shipping, total)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_order);
        $stmt->bind_param("ssssssssssdd", $user_id, $first_name, $last_name, $address, $city, $postal_code, $phone, $country, $payment_method, $subtotal, $shipping, $total);
        $stmt->execute();
        $order_id = $conn->insert_id;  // Get the ID of the newly inserted order

        // Insert each product into order_items table
        foreach ($cart as $item) {
            $insert_item = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_item);
            $stmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
            $stmt->execute();
        }

        // Clear user's cart after order is placed
        $clear_cart = "DELETE FROM cart WHERE user_id = ?";
        $stmt = $conn->prepare($clear_cart);
        $stmt->bind_param("s", $user_id);
        $stmt->execute();

        // Redirect to order_details.php with the order_id
        header("Location: order_details.php?order_id=" . $order_id);
        exit();
    } else {
        echo "<p style='color: red;'>Please fill in all required fields.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Cinnamon Skin Care</title>
    <link rel="stylesheet" href="checkout.css">
</head>
<body>
    <header>
        <h1>Checkout</h1>
    </header>

    <form method="POST" action="">
        <section id="billing-address">
            <h2>Billing Address</h2>
            <label>First Name: <input type="text" name="first_name" required></label>
            <label>Last Name: <input type="text" name="last_name" required></label>
            <label>Address: <input type="text" name="address" required></label>
            <label>City: <input type="text" name="city" required></label>
            <label>Postal Code: <input type="text" name="postal_code" required></label>
            <label>Phone: <input type="text" name="phone" required></label>
            <label>Country: <input type="text" name="country" required></label>
        </section>

        <section id="payment-method">
            <h2>Payment Method</h2>
            <label><input type="radio" name="payment" value="credit-card" onclick="toggleCreditCard(true)" checked> Credit Card</label>
            <label><input type="radio" name="payment" value="cash-on-delivery" onclick="toggleCreditCard(false)"> Cash on Delivery</label>

            <div id="credit-card-info">
                <label>Card Number: <input type="text" name="card-number"></label>
                <label>Expiry Date: <input type="month" name="expiry"></label>
                <label>CVV: <input type="text" name="cvv"></label>
            </div>
        </section>

        <section id="order-summary">
            <h2>Order Summary</h2>
            <p>Subtotal: <span>LKR <?= number_format($subtotal, 2) ?></span></p>
            <p>Shipping: <span>LKR <?= number_format($shipping, 2) ?></span></p>
            <p>Total: <span>LKR <?= number_format($total, 2) ?></span></p>
        </section>

        <button type="submit">Place Order</button>
        <button type="button" id="cancelButton">Cancel</button>
    </form>

    <script>
        function toggleCreditCard(show) {
            document.getElementById('credit-card-info').style.display = show ? 'block' : 'none';
        }
        document.getElementById('cancelButton').addEventListener('click', function () {
            window.location.href = 'cart.php';
        });
    </script>
</body>
</html>
