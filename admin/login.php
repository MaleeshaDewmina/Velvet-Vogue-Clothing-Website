<?php
session_start();
include '../includes/db.php';

if (isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // 🔹 Fetch the correct `id` column from the database
    $stmt = $conn->prepare("SELECT id, email, password FROM admins WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];  // ✅ FIXED: Use 'id' from DB
        $_SESSION['admin_email'] = $admin['email'];

        header("Location: index.php");
        exit();
    } else {
        $error = "❌ Invalid email or password!";
    }
}
?>
