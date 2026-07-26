<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php"); // ✅ Redirect if session not found
    exit();
}

include '../includes/db.php';

$admin_email = $_SESSION['admin_email']; // ✅ Fix variable

// Fetch Statistics
$totalProducts = $conn->query("SELECT COUNT(*) AS total FROM products")->fetch_assoc()['total'];
$totalOrders = $conn->query("SELECT COUNT(*) AS total FROM orders")->fetch_assoc()['total'];
$totalCustomers = $conn->query("SELECT COUNT(*) AS total FROM customers")->fetch_assoc()['total'];
$totalMessages = $conn->query("SELECT COUNT(*) AS total FROM messages")->fetch_assoc()['total']; 
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
    <h1>Welcome, <?php echo htmlspecialchars($admin_email); ?> 👋</h1>

    <div class="stats">
        <div class="stat-box"><h2><?php echo $totalProducts; ?></h2><p>Total Products</p></div>
        <div class="stat-box"><h2><?php echo $totalOrders; ?></h2><p>Total Orders</p></div>
        <div class="stat-box"><h2><?php echo $totalCustomers; ?></h2><p>Total Customers</p></div>
        <div class="stat-box"><h2><?php echo $totalMessages; ?></h2><p>New Messages</p></div>
    </div>

    <a href="logout.php" class="logout-btn">Logout</a>
</div>

</body>
</html>
