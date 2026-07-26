<?php
session_start();
include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_id = $_POST["order_id"];
    $status = $_POST["status"];

    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $order_id);

    if ($stmt->execute()) {
        header("Location: dashboard.php?success=Order status updated successfully");
        exit();
    } else {
        header("Location: dashboard.php?error=Error updating order status");
        exit();
    }
} else {
    header("Location: dashboard.php");
    exit();
}
