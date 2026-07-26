<?php
include 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $subject = trim($_POST["subject"]);
    $message = trim($_POST["message"]);

    // Insert message into database
    $stmt = $conn->prepare("INSERT INTO messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $subject, $message);
       

    if ($stmt->execute()) {
        header("Location: contact.php?success=Message sent successfully! Admin will reply to your email shortly.");
        exit();
    } else {
        header("Location: contact.php?error=❌ Failed to send message. Please try again.");
        exit();
    }
} else {
    header("Location: contact.php");
    exit();
}
?>
