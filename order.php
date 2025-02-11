<?php
session_start();
require_once 'dbconnection.php';

// Get user ID (logged-in user or guest session)
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : session_id();

// Fetch user orders
$sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY order_id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Cinnamon Skin Care</title>
    <link rel="stylesheet" href="order.css">
</head>
<body>
    <header>
        <h1>My Orders</h1>
    </header>

    <section id="order-list">
        <?php if ($orders): ?>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Order Date</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?php echo $order['order_id']; ?></td>
                            <td><?php echo date('d-m-Y', strtotime($order['order_date'])); ?></td>
                            <td>LKR <?php echo number_format($order['total'], 2); ?></td>
                            <td><?php echo ucfirst($order['status']); ?></td>
                            <td><a href="order_details.php?order_id=<?php echo $order['order_id']; ?>">View Details</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>You have no orders yet.</p>
        <?php endif; ?>
    </section>

</body>
</html>
