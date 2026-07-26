<?php 
include 'includes/navbar.php';
include 'includes/db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];

// Fetch user details
$stmt = $conn->prepare("SELECT name FROM customers WHERE id = ?");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$customer_name = $user['name'];

// Fetch user orders along with product details, size, and quantity
$orderQuery = $conn->prepare("
    SELECT o.id AS order_id, o.total_price, o.status, o.created_at, 
           oi.product_id, oi.size, oi.quantity, 
           p.name AS product_name, p.image
    FROM orders o 
    JOIN order_items oi ON o.id = oi.order_id 
    JOIN products p ON oi.product_id = p.id 
    WHERE o.customer_id = ? 
    ORDER BY o.created_at DESC
");
$orderQuery->bind_param("i", $customer_id);
$orderQuery->execute();
$orders = $orderQuery->get_result();


if (!$orders) {
    die("Error Fetching Orders: " . $conn->error); // Debugging
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders | Velvet Vogue</title>
    <link rel="stylesheet" href="css/dashboard.css?v=<?php echo time(); ?>">
</head>
<body>

<div class="dashboard-wrapper">
    <div class="dashboard-container">
        <h1>Welcome, <?php echo htmlspecialchars($customer_name); ?>!</h1>

        <h2>Your Orders</h2>

        <div class="order-table-container">
            <?php if ($orders->num_rows > 0) { ?>
                <table class="order-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Product</th>
                            <th>Image</th>
                            <th>Size</th> <!-- ✅ Added Size Column -->
                            <th>Quantity</th> <!-- ✅ Added Quantity Column -->
                            <th>Total Price</th>
                            <th>Status</th>
                            <th>Order Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $orders->fetch_assoc()) { ?>
                            <tr>
                                <td>#<?php echo $row['order_id']; ?></td>
                                <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                                <td>
                                    <img src="images/<?php echo htmlspecialchars($row['image']); ?>" class="order-product-img">
                                </td>
                                <td><?php echo isset($row['size']) ? htmlspecialchars($row['size']) : 'N/A'; ?></td>
                                <td><?php echo htmlspecialchars($row['quantity']); ?></td> <!-- ✅ Display Quantity -->
                                <td>Rs. <?php echo number_format($row['total_price'], 2); ?></td>
                                <td class="status <?php echo strtolower(str_replace(' ', '-', $row['status'])); ?>">
                                    <?php echo ucfirst($row['status']); ?>
                                </td>
                                <td><?php echo date("F j, Y", strtotime($row['created_at'])); ?></td>
                                <td>
                                    <?php if ($row['status'] === 'Delivered') { ?>
                                        <a href="product.php?id=<?php echo $row['product_id']; ?>#review-section" class="review-btn">
                                            Leave Review
                                        </a>
                                    <?php } else { ?>
                                        <span class="no-review">Not Available</span>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <p class="no-orders">You have no orders yet. <a href="index.php">Start Shopping</a></p>
            <?php } ?>
        </div>
    </div>
</div>

<div class="dashboard-buttons"></div>

</body>
</html>
