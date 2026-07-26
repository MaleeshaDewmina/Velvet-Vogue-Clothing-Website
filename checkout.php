<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
} 
include 'includes/navbar.php';
include 'includes/db.php';

$cart_items = $_SESSION['cart'] ?? [];
$total_price = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | Velvet Vogue</title>
    <link rel="stylesheet" href="css/checkout.css?v=<?php echo time(); ?>">
</head>
<body>

<!-- ✅ Preloader should be placed at the top -->
<div id="preloader">
    <img src="images/logo.png" alt="Velvet Vogue Logo" class="preloader-logo">
    <div class="dots">
        <span></span>
        <span></span>
        <span></span>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        let preloader = document.getElementById("preloader");

        // Ensure preloader stays visible for at least 2 seconds
        let minimumTime = 2000; // 2 seconds
        let startTime = Date.now();

        window.addEventListener("load", function () {
            let elapsedTime = Date.now() - startTime;
            let delayTime = Math.max(0, minimumTime - elapsedTime);

            setTimeout(() => {
                preloader.classList.add("hidden"); // Apply fade-out effect
                setTimeout(() => {
                    preloader.style.display = "none"; // Remove preloader after fading out
                }, 500);
            }, delayTime);
        });
    });
</script>

<div class="checkout-container">
    <h1>Checkout</h1>

    <?php if (isset($_GET['success'])): ?>
        <div class="success-message" onclick="this.style.display='none'">
            ✅ <?php echo htmlspecialchars($_GET['success']); ?> Click to dismiss.
        </div>
    <?php endif; ?>

    <?php if (empty($cart_items)): ?>
        <p>Your cart is empty. <a href="index.php">Go Shopping</a></p>
    <?php else: ?>
        <h2>Order Summary</h2>
        <table class="order-summary">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Product</th>
                    <th>Size</th> <!-- ✅ Added Size Column -->
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>

            <tbody>
                <?php 
                    $total_price = 0;
                    
                    foreach ($_SESSION['cart'] as $cart_key => $item) {
                    if (!is_array($item) || !isset($item["product_id"], $item["size"], $item["quantity"])) {
                        continue; // Skip invalid entries
                    }

                    $product_id = $item["product_id"];
                    $size = $item["size"];
                    $quantity = $item["quantity"];

                    // Fetch product details
                    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
                    $stmt->bind_param("i", $product_id);
                    $stmt->execute();
                    $product = $stmt->get_result()->fetch_assoc();

                    if (!$product) {
                        continue; // Skip if product is not found
                    }

                    $unit_price = $product['price'];
                    $subtotal = $unit_price * $quantity; // ✅ Fix: Correctly multiply unit price by quantity
                    $total_price += $subtotal;
                ?>
                    <tr>
                        <td><img src="images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="checkout-product-image"></td>
                        <td class="product-name"><?php echo htmlspecialchars($product['name']); ?></td>
                        <td class="product-size"><?php echo htmlspecialchars($size); ?></td> <!-- ✅ Added size column -->
                        <td class="product-qty"><?php echo $quantity; ?></td>
                        <td class="product-price">Rs. <?php echo number_format($unit_price, 2); ?></td>
                        <td class="product-total">Rs. <?php echo number_format($subtotal, 2); ?></td>
                    </tr>
                <?php } ?>
            </tbody>

        </table>

        <form action="process_checkout.php" method="POST" class="checkout-form">
            <h2>Billing Details</h2>
            <label for="name">Full Name:</label>
            <input type="text" name="name" required>

            <label for="address">Address:</label>
            <textarea name="address" required></textarea>

            <label for="phone">Phone Number:</label>
            <input type="text" name="phone" required>

            <h2>Select Payment Method</h2>
            <div class="payment-options">
                <label class="payment-label">
                    <input type="radio" name="payment_method" value="Cash on Delivery" required>
                    <img src="images/cash.png" alt="Cash on Delivery"> Cash on Delivery
                </label>
                <label class="payment-label">
                    <input type="radio" name="payment_method" value="Credit Card">
                    <img src="images/credit-card.png" alt="Credit Card"> Credit Card
                </label>
                <label class="payment-label">
                    <input type="radio" name="payment_method" value="PayPal">
                    <img src="images/paypal.png" alt="PayPal"> PayPal
                </label>
            </div>

            <h2>Total: Rs. <?php echo number_format($total_price, 2); ?></h2>
            <input type="hidden" name="total_price" value="<?php echo $total_price; ?>">

            <button type="submit" class="btn">Confirm Order</button>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
