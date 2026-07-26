<?php
include 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_name = $_POST['name'];
    $customer_email = $_POST['email'];
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Fetch product price
    $query = "SELECT price FROM products WHERE id = $product_id";
    $result = $conn->query($query);
    $product = $result->fetch_assoc();
    $total_price = $product['price'] * $quantity;

    // Insert order
    $sql = "INSERT INTO orders (customer_name, customer_email, product_id, quantity, total_price) 
            VALUES ('$customer_name', '$customer_email', '$product_id', '$quantity', '$total_price')";

    if ($conn->query($sql)) {
        echo "✅ Order placed successfully!";
    } else {
        echo "❌ Error: " . $conn->error;
    }
}

// Fetch all products for dropdown
$products = $conn->query("SELECT * FROM products");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Place Order</title>
</head>
<body>

    <h1>Place an Order</h1>
    <form action="" method="POST">
        <input type="text" name="name" placeholder="Your Name" required><br>
        <input type="email" name="email" placeholder="Your Email" required><br>
        
        <select name="product_id" required>
            <option value="">Select a Product</option>
            <?php while ($row = $products->fetch_assoc()) { ?>
                <option value="<?php echo $row['id']; ?>">
                    <?php echo $row['name']; ?> - $<?php echo $row['price']; ?>
                </option>
            <?php } ?>
        </select><br>

        <input type="number" name="quantity" min="1" placeholder="Quantity" required><br>
        <button type="submit">Place Order</button>
    </form>

</body>
</html>
