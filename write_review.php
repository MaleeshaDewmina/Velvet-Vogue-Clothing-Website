<?php
session_start();
include 'includes/db.php';

// Ensure user is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];

// Fetch delivered products
$stmt = $conn->prepare("
    SELECT oi.product_id, p.name, p.image, p.price 
    FROM orders o 
    JOIN order_items oi ON o.id = oi.order_id 
    JOIN products p ON oi.product_id = p.id 
    WHERE o.customer_id = ? AND o.status = 'Delivered'
");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$orders = $stmt->get_result();

if ($orders->num_rows === 0) {
    echo "<p class='error-message'>❌ You have no delivered orders to review.</p>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Write a Review | Velvet Vogue</title>
    <link rel="stylesheet" href="css/review.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

<div class="review-container">
    <h1>📝 Write a Review</h1>
    <p>Select a product to leave a review:</p>

    <div class="order-summary">
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($product = $orders->fetch_assoc()): ?>
                    <tr>
                        <td><img src="images/<?php echo $product['image']; ?>" class="product-img"></td>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td>Rs. <?php echo number_format($product['price'], 2); ?></td>
                        <td>
                            <button class="review-btn" onclick="showReviewForm(<?php echo $product['product_id']; ?>, '<?php echo htmlspecialchars($product['name']); ?>')">
                                Write Review
                            </button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Hidden Review Form -->
    <div id="review-form" class="hidden">
        <h2 id="product-name"></h2>
        <form action="submit_review.php" method="POST">
            <input type="hidden" name="product_id" id="product_id">
            
            <label>⭐ Rating:</label>
            <div class="star-rating">
                <input type="radio" name="rating" value="5" id="star5"><label for="star5">⭐</label>
                <input type="radio" name="rating" value="4" id="star4"><label for="star4">⭐</label>
                <input type="radio" name="rating" value="3" id="star3"><label for="star3">⭐</label>
                <input type="radio" name="rating" value="2" id="star2"><label for="star2">⭐</label>
                <input type="radio" name="rating" value="1" id="star1"><label for="star1">⭐</label>
            </div>

            <label>✍️ Your Review:</label>
            <textarea name="review_text" required></textarea>

            <button type="submit" class="btn">Submit Review</button>
            <button type="button" class="btn cancel-btn" onclick="hideReviewForm()">Cancel</button>
        </form>
    </div>
</div>

<script>
function showReviewForm(productId, productName) {
    document.getElementById("review-form").classList.remove("hidden");
    document.getElementById("product-name").textContent = "Review for " + productName;
    document.getElementById("product_id").value = productId;
}

function hideReviewForm() {
    document.getElementById("review-form").classList.add("hidden");
}
</script>

</body>
</html>
