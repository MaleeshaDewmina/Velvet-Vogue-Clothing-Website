<?php
session_start();
include '../includes/db.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: dashboard.php?error=Invalid Product ID");
    exit();
}

$product_id = $_GET['id'];

// Fetch product details
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    header("Location: dashboard.php?error=Product Not Found");
    exit();
}

// Fetch selected values
$selected_genders = [];
$genderStmt = $conn->prepare("SELECT gender FROM product_genders WHERE product_id = ?");
$genderStmt->bind_param("i", $product_id);
$genderStmt->execute();
$genderResult = $genderStmt->get_result();
while ($row = $genderResult->fetch_assoc()) {
    $selected_genders[] = $row['gender'];
}

$selected_clothing_types = [];
$clothingTypeStmt = $conn->prepare("SELECT clothing_type FROM product_clothing_types WHERE product_id = ?");
$clothingTypeStmt->bind_param("i", $product_id);
$clothingTypeStmt->execute();
$clothingTypeResult = $clothingTypeStmt->get_result();
while ($row = $clothingTypeResult->fetch_assoc()) {
    $selected_clothing_types[] = $row['clothing_type'];
}

$selected_sizes = [];
$sizeStmt = $conn->prepare("SELECT size FROM product_sizes WHERE product_id = ?");
$sizeStmt->bind_param("i", $product_id);
$sizeStmt->execute();
$sizeResult = $sizeStmt->get_result();
while ($row = $sizeResult->fetch_assoc()) {
    $selected_sizes[] = $row['size'];
}

$selected_categories = [];
$categoryStmt = $conn->prepare("SELECT category FROM product_categories WHERE product_id = ?");
$categoryStmt->bind_param("i", $product_id);
$categoryStmt->execute();
$categoryResult = $categoryStmt->get_result();
while ($row = $categoryResult->fetch_assoc()) {
    $selected_categories[] = $row['category'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $description = trim($_POST["description"]);
    $price = floatval($_POST["price"]);
    $genders = isset($_POST["gender"]) ? $_POST["gender"] : [];
    $clothing_types = isset($_POST["clothing_type"]) ? $_POST["clothing_type"] : [];
    $sizes = isset($_POST["size"]) ? $_POST["size"] : [];
    $categories = isset($_POST["category"]) ? $_POST["category"] : [];

    // Update product details
    $stmt = $conn->prepare("UPDATE products SET name=?, description=?, price=? WHERE id=?");
    $stmt->bind_param("ssdi", $name, $description, $price, $product_id);
    $stmt->execute();

    // Remove old selections
    $conn->query("DELETE FROM product_genders WHERE product_id = $product_id");
    $conn->query("DELETE FROM product_clothing_types WHERE product_id = $product_id");
    $conn->query("DELETE FROM product_sizes WHERE product_id = $product_id");
    $conn->query("DELETE FROM product_categories WHERE product_id = $product_id");

    // Insert new selections
    foreach ($genders as $gender) {
        $stmt = $conn->prepare("INSERT INTO product_genders (product_id, gender) VALUES (?, ?)");
        $stmt->bind_param("is", $product_id, $gender);
        $stmt->execute();
    }

    foreach ($clothing_types as $type) {
        $stmt = $conn->prepare("INSERT INTO product_clothing_types (product_id, clothing_type) VALUES (?, ?)");
        $stmt->bind_param("is", $product_id, $type);
        $stmt->execute();
    }

    foreach ($sizes as $size) {
        $stmt = $conn->prepare("INSERT INTO product_sizes (product_id, size) VALUES (?, ?)");
        $stmt->bind_param("is", $product_id, $size);
        $stmt->execute();
    }

    foreach ($categories as $category) {
        $stmt = $conn->prepare("INSERT INTO product_categories (product_id, category) VALUES (?, ?)");
        $stmt->bind_param("is", $product_id, $category);
        $stmt->execute();
    }

    header("Location: dashboard.php?success=Product updated successfully!");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product | Admin</title>
    <link rel="stylesheet" href="../css/admin_form.css?v=<?php echo time(); ?>">
</head>
<body>

<div class="admin-form-container">
    <h2>Edit Product</h2>
    <form action="" method="POST" enctype="multipart/form-data" class="admin-form">
        <label>Product Name:</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>

        <label>Description:</label>
        <textarea name="description" required><?php echo htmlspecialchars($product['description']); ?></textarea>

        <label>Price (Rs.):</label>
        <input type="number" name="price" step="0.01" value="<?php echo $product['price']; ?>" required>

        <label>Gender:</label>
        <table class="checkbox-table">
            <tr>
                <td><input type="checkbox" name="gender[]" value="Male" <?php echo in_array("Male", $selected_genders) ? 'checked' : ''; ?>> Male</td>
                <td><input type="checkbox" name="gender[]" value="Female" <?php echo in_array("Female", $selected_genders) ? 'checked' : ''; ?>> Female</td>
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
                    $checked = in_array($type, $selected_clothing_types) ? 'checked' : '';
                    echo '<td><input type="checkbox" name="clothing_type[]" value="' . $type . '" ' . $checked . '> ' . $type . '</td>';
                }
                echo "</tr>";
            }
            ?>
        </table>

        <label>Size:</label>
        <table class="checkbox-table">
            <tr>
                <td><input type="checkbox" name="size[]" value="XS" <?php echo in_array("XS", $selected_sizes) ? 'checked' : ''; ?>> XS</td>
                <td><input type="checkbox" name="size[]" value="S" <?php echo in_array("S", $selected_sizes) ? 'checked' : ''; ?>> S</td>
                <td><input type="checkbox" name="size[]" value="M" <?php echo in_array("M", $selected_sizes) ? 'checked' : ''; ?>> M</td>
                <td><input type="checkbox" name="size[]" value="L" <?php echo in_array("L", $selected_sizes) ? 'checked' : ''; ?>> L</td>
                <td><input type="checkbox" name="size[]" value="XL" <?php echo in_array("XL", $selected_sizes) ? 'checked' : ''; ?>> XL</td>
                <td><input type="checkbox" name="size[]" value="XXL" <?php echo in_array("XXL", $selected_sizes) ? 'checked' : ''; ?>> XXL</td>
            </tr>
        </table>

        <button type="submit" class="admin-btn submit-btn">Update Product</button>
    </form>
</div>

</body>
</html>