<?php
session_start();

if (isset($_GET["id"])) {
    $cart_key = $_GET["id"];

    if (isset($_SESSION["cart"][$cart_key])) {
        unset($_SESSION["cart"][$cart_key]); // Remove item from cart
    }

    header("Location: cart.php");
    exit();
} else {
    header("Location: index.php");
    exit();
}
?>
