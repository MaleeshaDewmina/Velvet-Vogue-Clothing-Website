<?php
include '../includes/db.php';

$username = "admin";
$plain_password = "admin123"; // Change password as needed
$hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);

echo "Hashed Password: " . $hashed_password;

// Insert into database
$conn->query("INSERT INTO admins (username, password) VALUES ('$username', '$hashed_password')");
?>
