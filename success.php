<?php
session_start();
if (!isset($_GET['order_id'])) {
    header("Location: index.php");
    exit();
}
$order_id = intval($_GET['order_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Placed - Velvet Vogue</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <h1>Thank You!</h1>
    <p>Your order #<?php echo $order_id; ?> has been placed successfully.</p>
    <a href="index.php" class="btn">Return to Home</a>

</body>
</html>
