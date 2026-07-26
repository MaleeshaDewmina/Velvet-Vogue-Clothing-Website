<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']);

    // First, delete the product from `order_items` to prevent foreign key constraint error
    $conn->query("DELETE FROM order_items WHERE product_id = $product_id");

    // Then, delete the product from `products`
    $deleteQuery = $conn->prepare("DELETE FROM products WHERE id = ?");
    $deleteQuery->bind_param("i", $product_id);

    if ($deleteQuery->execute()) {
        header("Location: dashboard.php?deleted=success");
        exit();
    } else {
        echo "Error deleting product.";
    }
} else {
    echo "Invalid request.";
}
?>
