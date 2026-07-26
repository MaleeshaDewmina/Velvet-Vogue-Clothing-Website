<?php
session_start();
include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $description = trim($_POST["description"]);
    $price = floatval($_POST["price"]);
    $genders = isset($_POST["gender"]) ? $_POST["gender"] : [];
    $clothing_types = isset($_POST["clothing_type"]) ? $_POST["clothing_type"] : [];
    $sizes = isset($_POST["size"]) ? $_POST["size"] : [];
    $categories = isset($_POST["category"]) ? $_POST["category"] : [];

    // Image Upload
    $image_name = basename($_FILES["image"]["name"]);
    $target_dir = "../images/";
    $target_file = $target_dir . $image_name;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        // Insert product into products table
        $stmt = $conn->prepare("INSERT INTO products (name, description, price, image) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssds", $name, $description, $price, $image_name);
        $stmt->execute();
        $product_id = $stmt->insert_id; // Get the last inserted product ID

        // Insert Genders
        foreach ($genders as $gender) {
            $stmt = $conn->prepare("INSERT INTO product_genders (product_id, gender) VALUES (?, ?)");
            $stmt->bind_param("is", $product_id, $gender);
            $stmt->execute();
        }

        // Insert Clothing Types
        foreach ($clothing_types as $type) {
            $stmt = $conn->prepare("INSERT INTO product_clothing_types (product_id, clothing_type) VALUES (?, ?)");
            $stmt->bind_param("is", $product_id, $type);
            $stmt->execute();
        }

        // Insert Sizes
        foreach ($sizes as $size) {
            $stmt = $conn->prepare("INSERT INTO product_sizes (product_id, size) VALUES (?, ?)");
            $stmt->bind_param("is", $product_id, $size);
            $stmt->execute();
        }

        // Insert Categories
        foreach ($categories as $category) {
            $stmt = $conn->prepare("INSERT INTO product_categories (product_id, category) VALUES (?, ?)");
            $stmt->bind_param("is", $product_id, $category);
            $stmt->execute();
        }

        header("Location: dashboard.php?success=Product added successfully!");
        exit();
    } else {
        echo "❌ Error: Image upload failed.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product | Admin</title>
    <link rel="stylesheet" href="../css/admin_form.css?v=<?php echo time(); ?>">
</head>
<body>

<div class="admin-form-container">
    <h2>Add New Product</h2>
    <form action="" method="POST" enctype="multipart/form-data" class="admin-form">
        <label>Product Name:</label>
        <input type="text" name="name" required>

        <label>Description:</label>
        <textarea name="description" required></textarea>

        <label>Price (Rs.):</label>
        <input type="number" name="price" step="0.01" required>

        <label>Gender:</label>
        <table class="checkbox-table">
            <tr>
                <td><input type="checkbox" name="gender[]" value="Male"> Male</td>
                <td><input type="checkbox" name="gender[]" value="Female"> Female</td>
            </tr>
        </table>

        <label>Clothing Type:</label>
        <table class="checkbox-table">
            <?php
            $clothingTypes = ["T-shirt", "Jeans", "Sweater", "Hoodie", "Jacket", "Shorts", "Sneakers", "Tank Top", "Pajama Set",
                "Leggings", "Dress", "Button-Down Shirt", "Cardigan", "Sweatpants", "Chinos", "Boots", "Blazer", "Scarf", "Cap", "Socks"];
            foreach (array_chunk($clothingTypes, 5) as $chunk) {
                echo "<tr>";
                foreach ($chunk as $type) {
                    echo '<td><input type="checkbox" name="clothing_type[]" value="' . $type . '"> ' . $type . '</td>';
                }
                echo "</tr>";
            }
            ?>
        </table>

        <label>Size:</label>
        <table class="checkbox-table">
            <tr>
                <td><input type="checkbox" name="size[]" value="XS"> XS</td>
                <td><input type="checkbox" name="size[]" value="S"> S</td>
                <td><input type="checkbox" name="size[]" value="M"> M</td>
                <td><input type="checkbox" name="size[]" value="L"> L</td>
                <td><input type="checkbox" name="size[]" value="XL"> XL</td>
                <td><input type="checkbox" name="size[]" value="XXL"> XXL</td>
            </tr>
        </table>

        <label>Category:</label>
        <table class="checkbox-table">
            <?php
            $categoriesQuery = $conn->query("SELECT DISTINCT category FROM product_categories");
            while ($row = $categoriesQuery->fetch_assoc()) {
                echo '<tr><td><input type="checkbox" name="category[]" value="' . $row['category'] . '"> ' . $row['category'] . '</td></tr>';
            }
            ?>
        </table>

        <label>Upload Image:</label>
        <input type="file" name="image" accept="image/*" required>

        <div class="admin-btn-container">
            <button type="submit" class="admin-btn submit-btn">Add Product</button>
            <a href="dashboard.php" class="admin-btn cancel-btn">Cancel</a>
        </div>
    </form>
</div>

</body>
</html>