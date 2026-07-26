<?php
session_start();
include 'includes/db.php';

$category = isset($_GET['category']) ? $_GET['category'] : '';

// Fetch products that match the category from `product_categories`
$productQuery = $conn->prepare("
    SELECT p.* FROM products p
    JOIN product_categories pc ON p.id = pc.product_id
    WHERE pc.category = ?
");
$productQuery->bind_param("s", $category);
$productQuery->execute();
$products = $productQuery->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($category); ?> | Velvet Vogue</title>
    <link rel="stylesheet" href="css/home.css?v=<?php echo time(); ?>">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="product-container">
    <h2><?php echo htmlspecialchars($category); ?></h2>
    
    <?php if ($products->num_rows > 0): ?>
        <div class="product-grid">
            <?php while ($product = $products->fetch_assoc()) { ?>
                <div class="product-card">
                    <img src="images/<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p>Rs. <?php echo number_format($product['price'], 2); ?></p>
                    <a href="product.php?id=<?php echo $product['id']; ?>" class="view-btn">View Product</a>
                </div>
            <?php } ?>
        </div>
    <?php else: ?>
        <p>No products found in this category.</p>
    <?php endif; ?>
</div>

</body>
</html>
