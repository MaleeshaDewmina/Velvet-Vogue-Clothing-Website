<?php
include 'includes/db.php';

$conditions = [];
$params = [];
$types = "";

// Decode JSON inputs
$gender = isset($_POST['gender']) ? json_decode($_POST['gender'], true) : [];
$clothingType = isset($_POST['clothing_type']) ? json_decode($_POST['clothing_type'], true) : [];
$size = isset($_POST['size']) ? json_decode($_POST['size'], true) : [];
$maxPrice = isset($_POST['max_price']) ? (float)$_POST['max_price'] : 20000;

// Base query
$sql = "SELECT DISTINCT p.* FROM products p
        LEFT JOIN product_genders pg ON p.id = pg.product_id
        LEFT JOIN product_clothing_types pct ON p.id = pct.product_id
        LEFT JOIN product_sizes ps ON p.id = ps.product_id";

// Apply filters dynamically
if (!empty($gender)) {
    $placeholders = implode(',', array_fill(0, count($gender), '?'));
    $conditions[] = "pg.gender IN ($placeholders)";
    $params = array_merge($params, $gender);
    $types .= str_repeat("s", count($gender));  // "s" for string type
}

if (!empty($clothingType)) {
    $placeholders = implode(',', array_fill(0, count($clothingType), '?'));
    $conditions[] = "pct.clothing_type IN ($placeholders)";
    $params = array_merge($params, $clothingType);
    $types .= str_repeat("s", count($clothingType));
}

if (!empty($size)) {
    $placeholders = implode(',', array_fill(0, count($size), '?'));
    $conditions[] = "ps.size IN ($placeholders)";
    $params = array_merge($params, $size);
    $types .= str_repeat("s", count($size));
}

// Add price filter
$conditions[] = "p.price <= ?";
$params[] = $maxPrice;
$types .= "d"; // "d" for decimal/float

// Apply WHERE conditions if filters exist
if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$stmt = $conn->prepare($sql);

// Bind parameters dynamically
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

// Display filtered products
if ($result->num_rows > 0) {
    while ($product = $result->fetch_assoc()) {
        echo '
        <div class="product">
            <img src="images/' . htmlspecialchars($product['image']) . '" alt="' . htmlspecialchars($product['name']) . '">
            <h3>' . htmlspecialchars($product['name']) . '</h3>
            <p>Rs. ' . number_format($product['price'], 2) . '</p>
            <a href="product.php?id=' . $product['id'] . '" class="btn">View</a>
        </div>';
    }
} else {
    echo "<p>No products found.</p>";
}
?>
