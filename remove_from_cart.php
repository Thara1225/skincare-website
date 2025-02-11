 
<?php

session_start();

require_once 'dbconnection.php';



if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['cart_id'])) {

        $cart_id = $_POST['cart_id'];



        $sql = "DELETE FROM cart WHERE cart_id = ?";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param("i", $cart_id);

        $stmt->execute();



        header("Location: cart.php");

        exit();

    } else {

        echo "Cart ID is missing.";

    }

}

?>
