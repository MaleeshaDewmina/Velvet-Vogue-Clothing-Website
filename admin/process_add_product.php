<?php
include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $description = trim($_POST["description"]);
    $price = floatval($_POST["price"]);
    $category = trim($_POST["category"]);
    $image_name = basename($_FILES["image"]["name"]);

    // Handle Image Upload
    $target_dir = "../images/";
    $target_file = $target_dir . $image_name;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        // Insert product into the database
        $stmt = $conn->prepare("INSERT INTO products (name, description, price, category, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdss", $name, $description, $price, $category, $image_name);

        if ($stmt->execute()) {
            // Redirect to dashboard with success message
            header("Location: dashboard.php?success=Product added successfully!");
            exit();

        } else {
            echo "❌ Error inserting product: " . $stmt->error;
        }
    } else {
        echo "❌ Error: Image upload failed.";
    }
} else {
    header("Location: add_product.php");
    exit();
}
?>
