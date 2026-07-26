<?php 
include 'includes/navbar.php';
include 'includes/db.php';

if (!isset($_GET['id'])) {
    echo "<p>Product not found.</p>";
    exit();
}

$product_id = $_GET['id'];

// Fetch product details
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    echo "<p>Product not found.</p>";
    exit();
}

// ✅ Fetch Sizes from product_sizes table
$sizeQuery = $conn->prepare("SELECT size FROM product_sizes WHERE product_id = ?");
$sizeQuery->bind_param("i", $product_id);
$sizeQuery->execute();
$sizeResult = $sizeQuery->get_result();
$sizes = [];

while ($row = $sizeResult->fetch_assoc()) {
    $sizes[] = $row['size'];
}

// ✅ Fetch Reviews
$reviews_stmt = $conn->prepare("SELECT r.rating, r.review_text, r.created_at, c.name 
                                FROM reviews r 
                                JOIN customers c ON r.customer_id = c.id 
                                WHERE r.product_id = ? 
                                ORDER BY r.created_at DESC");
$reviews_stmt->bind_param("i", $product_id);
$reviews_stmt->execute();
$reviews_result = $reviews_stmt->get_result();

// ✅ Check if user can leave a review
$can_review = false;
if (isset($_SESSION['customer_id'])) {
    $customer_id = $_SESSION['customer_id'];
    $order_stmt = $conn->prepare("SELECT o.id FROM orders o 
                                  JOIN order_items oi ON o.id = oi.order_id 
                                  WHERE o.customer_id = ? AND oi.product_id = ? AND o.status = 'Delivered'");
    $order_stmt->bind_param("ii", $customer_id, $product_id);
    $order_stmt->execute();
    $order_result = $order_stmt->get_result();
    $can_review = ($order_result->num_rows > 0);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> | Velvet Vogue</title>
    <link rel="stylesheet" href="css/product.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

<div class="product-container">
    <div class="product-left">
        <img src="images/<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-img">
    </div>

    <div class="product-right">
        <h1><?php echo htmlspecialchars($product['name']); ?></h1>
        <p class="product-description"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
        <p class="product-price">Rs. <?php echo number_format($product['price'], 2); ?></p>

        
</select>


<form action="add_to_cart.php" method="POST">
    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
    
    <label for="size">Select Size:</label>
    <select name="size" id="size" class="styled-size-dropdown" required>
        <?php
        $sizeStmt = $conn->prepare("SELECT size FROM product_sizes WHERE product_id = ?");
        $sizeStmt->bind_param("i", $product['id']);
        $sizeStmt->execute();
        $sizes = $sizeStmt->get_result();
        
        while ($sizeRow = $sizes->fetch_assoc()) {
            echo "<option value='" . htmlspecialchars($sizeRow['size']) . "'>" . htmlspecialchars($sizeRow['size']) . "</option>";
        }
        ?>
    </select>
    
    <label for="quantity">Quantity:</label>
    <input type="number" name="quantity" id="quantity" value="1" min="1" class="quantity-input" required>
    
    <button type="submit" class="btn add-to-cart">Add to Cart</button>
</form>



        <a href="index.php" class="back-btn">
            <i class="fa fa-arrow-left"></i> Back to Shop
        </a>

    </div>
</div> 

<!-- Reviews Section -->
<div class="review-section">
    <h2>Customer Reviews</h2>
    <?php if ($reviews_result->num_rows > 0): ?>
        <div class="reviews">
            <?php while ($review = $reviews_result->fetch_assoc()): ?>
                
                    <div class="review-content">
                        <p class="review-user"><?php echo htmlspecialchars($review['name']); ?> 
                            <small>(<?php echo date("F j, Y", strtotime($review['created_at'])); ?>)</small>
                        </p>
                        
                        <p class="star-rating">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fa fa-star <?php echo ($i <= $review['rating']) ? 'checked' : ''; ?>"></i>
                            <?php endfor; ?>
                        </p>
                        
                        <p class="review-text"><?php echo nl2br(htmlspecialchars($review['review_text'])); ?></p>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>No reviews yet. Be the first to review this product by ordering and getting it Delivered!</p>
    <?php endif; ?>
</div>

<!-- Leave a Review -->
<?php if ($can_review): ?>
    <div class="review-form">
        <h2>Leave a Review</h2>
        <form action="submit_review.php" method="POST">
            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
            <label>Rating:</label>
            <select name="rating" required>
                <option value="5">⭐⭐⭐⭐⭐ - Excellent</option>
                <option value="4">⭐⭐⭐⭐ - Good</option>
                <option value="3">⭐⭐⭐ - Average</option>
                <option value="2">⭐⭐ - Poor</option>
                <option value="1">⭐ - Terrible</option>
            </select>
            <label>Review:</label>
            <textarea name="review_text" required></textarea>
            <button type="submit" class="btn">Submit Review</button>
        </form>
    </div>
<?php else: ?>
    <p class="review-restriction">*Only customers who have received this product can leave a review.</p>
<?php endif; ?>

</body>
</html>
