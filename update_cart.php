<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["product_id"], $_POST["size"], $_POST["quantity"])) {
    $product_id = $_POST["product_id"];
    $size = $_POST["size"];
    $quantity = intval($_POST["quantity"]);

    if ($quantity < 1) {
        $quantity = 1; // Ensure minimum quantity of 1
    }

    $cart_key = "{$product_id}_{$size}"; // Create key with product ID and size

    if (isset($_SESSION["cart"][$cart_key])) {
        $_SESSION["cart"][$cart_key]["quantity"] = $quantity;
    }

    header("Location: cart.php");
    exit();
} else {
    header("Location: index.php");
    exit();
}
?>
