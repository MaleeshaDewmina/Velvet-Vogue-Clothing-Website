<?php
session_start();
include 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Check if email already exists
    $checkStmt = $conn->prepare("SELECT id FROM customers WHERE email = ?");
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        $error = "Email already exists! Try logging in.";
    } else {
        // Hash password before storing
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO customers (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $hashed_password);

        if ($stmt->execute()) {
            $_SESSION['customer_id'] = $stmt->insert_id;
            $_SESSION['customer_name'] = $name;
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Registration failed. Try again!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Velvet Vogue</title>
    <link rel="stylesheet" href="css/auth.css?v=<?php echo time(); ?>">
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-container">
        <h2>Create Your Account</h2>
        <?php if (isset($error)) echo "<p class='error' style='color:red;'>$error</p>"; ?>
        
        <form method="post">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            
            <div class="password-container">
                <input type="password" id="password" name="password" placeholder="Create Password" required>
                <span class="toggle-password" onclick="togglePassword()">👁️</span>
            </div>

            <button type="submit">Register</button>
        </form>

        <p>Already have an account? <a href="login.php">Log In</a></p>
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
