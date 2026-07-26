<?php
session_start();
include 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $address = trim($_POST["address"]);
    $phone = trim($_POST["phone"]);
    $total_price = floatval($_POST["total_price"]);
    $payment_method = $_POST["payment_method"] ?? null;

    // Check if payment method is selected
    if (empty($payment_method)) {
        header("Location: checkout.php?error=Please select a payment method");
        exit();
    }

    // Ensure all fields are filled
    if (empty($name) || empty($address) || empty($phone) || empty($total_price)) {
        header("Location: checkout.php?error=Please fill all required fields");
        exit();
    }

    // Insert order into database
    $stmt = $conn->prepare("INSERT INTO orders (customer_id, name, address, phone, total_price, payment_method, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'Pending', NOW())");
    $stmt->bind_param("isssds", $_SESSION['customer_id'], $name, $address, $phone, $total_price, $payment_method);

    if ($stmt->execute()) {
        $order_id = $stmt->insert_id;

        // Insert order items
        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $key => $cart_item) {
                if (is_array($cart_item)) {
                    $product_id = $cart_item["product_id"];
                    $size = $cart_item["size"];
                    $quantity = $cart_item["quantity"];

                    // Get product price
                    $product_stmt = $conn->prepare("SELECT price FROM products WHERE id = ?");
                    $product_stmt->bind_param("i", $product_id);
                    $product_stmt->execute();
                    $product_result = $product_stmt->get_result()->fetch_assoc();
                    $unit_price = $product_result['price'];

                    // Insert into order_items
                    $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, size, quantity, unit_price) VALUES (?, ?, ?, ?, ?)");
                    $item_stmt->bind_param("iisid", $order_id, $product_id, $size, $quantity, $unit_price);
                    $item_stmt->execute();
                }
            }
        }

        // Clear cart session
        unset($_SESSION['cart']);

        // Redirect with success message
        header("Location: checkout.php?success=Order placed successfully! Check your dashboard for updates.");
        exit();
    } else {
        header("Location: checkout.php?error=Something went wrong. Try again.");
        exit();
    }
} else {
    header("Location: checkout.php");
    exit();
}
?>
