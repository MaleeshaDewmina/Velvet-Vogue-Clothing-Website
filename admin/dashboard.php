<?php
session_start();

// Redirect if admin is NOT logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

include '../includes/db.php';

// Fetch statistics
$totalProducts = $conn->query("SELECT COUNT(*) AS total FROM products")->fetch_assoc()['total'];
$totalOrders = $conn->query("SELECT COUNT(*) AS total FROM orders")->fetch_assoc()['total'];
$totalCustomers = $conn->query("SELECT COUNT(*) AS total FROM customers")->fetch_assoc()['total'];
$totalMessages = $conn->query("SELECT COUNT(*) AS total FROM messages WHERE status = 'unread'")->fetch_assoc()['total']; // Only unread messages
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Velvet Vogue</title>
    <link rel="stylesheet" href="../css/admin.css?v=<?php echo time(); ?>">
</head>
<body>

<div class="admin-container">
    <h1>Admin Dashboard</h1>

    <div class="stats">
        <div class="stat-box"><h2><?php echo $totalProducts; ?></h2><p>Total Products</p></div>
        <div class="stat-box"><h2><?php echo $totalOrders; ?></h2><p>Total Orders</p></div>
        <div class="stat-box"><h2><?php echo $totalCustomers; ?></h2><p>Total Customers</p></div>
        <div class="stat-box"><h2><?php echo $totalMessages; ?></h2><p>New Messages</p></div>
    </div>

    <a href="add_product.php" class="btn add-product-btn">➕ Add New Product</a>

    <h2>Manage Products</h2>
    <table class="dashboard-table">
        <thead>
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Price</th>
                <th>Category</th>
                <th>Gender</th>
                <th>Clothing Type</th>
                <th>Size</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
                // Modified query to fetch the associated categories
                $productQuery = $conn->query("
                SELECT p.id, p.name, p.price, p.image,
                GROUP_CONCAT(DISTINCT pg.gender SEPARATOR ', ') AS genders,
                GROUP_CONCAT(DISTINCT pct.clothing_type SEPARATOR ', ') AS clothing_types,
                GROUP_CONCAT(DISTINCT ps.size SEPARATOR ', ') AS sizes,
                GROUP_CONCAT(DISTINCT pc.category SEPARATOR ', ') AS categories
                FROM products p
                LEFT JOIN product_genders pg ON p.id = pg.product_id
                LEFT JOIN product_clothing_types pct ON p.id = pct.product_id
                LEFT JOIN product_sizes ps ON p.id = ps.product_id
                LEFT JOIN product_categories pc ON p.id = pc.product_id
                GROUP BY p.id
                ORDER BY p.id DESC");
            
                while ($product = $productQuery->fetch_assoc()) { ?>
                <tr>
                    <td><img src="../images/<?php echo $product['image']; ?>" width="50"></td>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td>Rs. <?php echo number_format($product['price'], 2); ?></td>
                    <td><?php echo htmlspecialchars($product['categories'] ?: 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($product['genders'] ?: 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($product['clothing_types'] ?: 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($product['sizes'] ?: 'N/A'); ?></td>
                    <td>
                        <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="edit-btn">Edit</a>
                        <a href="delete_product.php?id=<?php echo $product['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php } ?>

        </tbody>
    </table>

    <h2>Manage Orders</h2>
    <table class="dashboard-table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Total Price</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $orderQuery = $conn->query("SELECT * FROM orders ORDER BY created_at DESC");
            while ($order = $orderQuery->fetch_assoc()) { ?>
                <tr>
                    <td>#<?php echo $order['id']; ?></td>
                    <td><?php echo htmlspecialchars($order['name']); ?></td>
                    <td>Rs. <?php echo number_format($order['total_price'], 2); ?></td>
                    <td class="status <?php echo strtolower($order['status']); ?>"><?php echo ucfirst($order['status']); ?></td>
                    <td><a href="order_details.php?id=<?php echo $order['id']; ?>" class="update-btn">Update</a></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <h2>Customer Messages</h2>
    <table class="dashboard-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Subject</th>
                <th>Message</th>
                <th>Sent On</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $messageQuery = $conn->query("SELECT * FROM messages ORDER BY created_at DESC");
            if ($messageQuery->num_rows > 0) {
                while ($message = $messageQuery->fetch_assoc()) { ?>
                    <tr class="<?php echo $message['status'] == 'unread' ? 'message-unread' : ''; ?>">
                        <td><?php echo htmlspecialchars($message['name']); ?></td>
                        <td><?php echo htmlspecialchars($message['email']); ?></td>
                        <td><?php echo htmlspecialchars($message['subject']); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($message['message'])); ?></td>
                        <td><?php echo htmlspecialchars($message['created_at']); ?></td>
                    </tr>
                <?php }
            } else { ?>
                <tr><td colspan="6">No messages yet.</td></tr>
            <?php } ?>
        </tbody>
    </table>

    <a href="logout.php" class="logout-btn">Logout</a>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>