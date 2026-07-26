<?php
include 'includes/db.php';
session_start();

if (!isset($_SESSION['customer_id']) || $_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: index.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];
$product_id = $_POST['product_id'];
$rating = $_POST['rating'];
$review_text = trim($_POST['review_text']);

// Check if Customer Already Left a Review
$stmt = $conn->prepare("SELECT * FROM reviews WHERE customer_id = ? AND product_id = ?");
$stmt->bind_param("ii", $customer_id, $product_id);
$stmt->execute();
$existing_review = $stmt->get_result();

if ($existing_review->num_rows > 0) {
    header("Location: product.php?id=$product_id&error=You have already reviewed this product.");
    exit();
}

// Insert Review
$stmt = $conn->prepare("INSERT INTO reviews (customer_id, product_id, rating, review_text) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iiis", $customer_id, $product_id, $rating, $review_text);
if ($stmt->execute()) {
    header("Location: product.php?id=$product_id&success=Review submitted successfully!");
} else {
    header("Location: product.php?id=$product_id&error=Failed to submit review.");
}
exit();
