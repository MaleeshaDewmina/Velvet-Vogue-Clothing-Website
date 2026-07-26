<?php
session_start();
include '../includes/db.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: dashboard.php?error=Invalid Order ID");
    exit();
}

$order_id = $_GET['id'];

// Fetch order details
$orderStmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$orderStmt->bind_param("i", $order_id);
$orderStmt->execute();
$order = $orderStmt->get_result()->fetch_assoc();

if (!$order) {
    header("Location: dashboard.php?error=Order Not Found");
    exit();
}

// Fetch ordered products including size
$productStmt = $conn->prepare("SELECT p.image, p.name, oi.size, oi.quantity, oi.unit_price
                               FROM order_items oi 
                               JOIN products p ON oi.product_id = p.id 
                               WHERE oi.order_id = ?");
$productStmt->bind_param("i", $order_id);
$productStmt->execute();
$products = $productStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details | Velvet Vogue</title>
    <link rel="stylesheet" href="../css/admin.css?v=<?php echo time(); ?>">
</head>
<body>

<div class="order-details-container">
    <h1>Order Details</h1>

    <p><strong>Customer:</strong> <?php echo htmlspecialchars($order['name']); ?></p>
    <p><strong>Address:</strong> <?php echo nl2br(htmlspecialchars($order['address'])); ?></p>
    <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
    <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
    
    <h2>Ordered Products</h2>
    <table class="dashboard-table">
        <thead>
            <tr>
                <th>Image</th>
                <th>Product</th>
                <th>Size</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($product = $products->fetch_assoc()) { ?>
                <tr>
                    <td><img src="../images/<?php echo htmlspecialchars($product['image']); ?>" class="order-product-img"></td>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo htmlspecialchars($product['size']); ?></td>
                    <td><?php echo $product['quantity']; ?></td>
                    <td>Rs. <?php echo number_format($product['unit_price'], 2); ?></td>
                    <td>Rs. <?php echo number_format($product['quantity'] * $product['unit_price'], 2); ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <h2>Update Order Status</h2>
    <form method="POST" action="update_order.php">
        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
        <select name="status" class="order-status-dropdown">
            <option value="Pending" <?php if ($order['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
            <option value="Approved" <?php if ($order['status'] == 'Approved') echo 'selected'; ?>>Approved</option>
            <option value="Shipped" <?php if ($order['status'] == 'Shipped') echo 'selected'; ?>>Shipped</option>
            <option value="Delivered" <?php if ($order['status'] == 'Delivered') echo 'selected'; ?>>Delivered</option>
            <option value="Cancelled" <?php if ($order['status'] == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
        </select>
        <button type="submit" class="update-status-btn">Update</button>
    </form>

    <a href="dashboard.php" class="back-btn">Back to Orders</a>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>