<?php
session_start();
include 'includes/db.php'; // ✅ Ensure the correct database connection

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // ✅ Check if user is an Admin
    $stmt = $conn->prepare("SELECT id, email, password FROM admins WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_email'] = $admin['email'];

        header("Location: admin/dashboard.php"); // ✅ Admin Redirect
        exit();
    }

    // ✅ Check if user is a Customer
    $stmt = $conn->prepare("SELECT id, email, password FROM customers WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $customer = $result->fetch_assoc();

    if ($customer && password_verify($password, $customer['password'])) {
        $_SESSION['customer_id'] = $customer['id'];
        $_SESSION['customer_email'] = $customer['email'];

        header("Location: dashboard.php"); // ✅ Customer Redirect
        exit();
    }

    // ❌ If no match, show error
    $error = "❌ Invalid email or password!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Velvet Vogue</title>
    <link rel="stylesheet" href="css/auth.css?v=<?php echo time(); ?>"> <!-- Ensure updated styles -->
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-container">
        <h2>Welcome Back!</h2>
        <?php if (isset($error)) echo "<p class='error' style='color:red;'>$error</p>"; ?>
        
        <form method="post">
            <input type="email" name="email" placeholder="Enter Your Email" required>
            
            <div class="password-container">
                <input type="password" id="password" name="password" placeholder="Enter Password" required>
                <span class="toggle-password" onclick="togglePassword()">👁️</span>
            </div>

            <button type="submit">Login</button>
        </form>

        <p>Don't have an account? <a href="register.php">Sign Up</a></p>
    </div>
</div>

<script>
function togglePassword() {
    var passField = document.getElementById("password");
    passField.type = (passField.type === "password") ? "text" : "password";
}
</script>

</body>
</html>
