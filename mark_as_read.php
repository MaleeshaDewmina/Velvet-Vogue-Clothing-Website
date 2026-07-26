<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id'])) {
    $message_id = $_GET['id'];
    
    $stmt = $conn->prepare("UPDATE messages SET status = 'read' WHERE id = ?");
    $stmt->bind_param("i", $message_id);

    if ($stmt->execute()) {
        header("Location: dashboard.php?success=Message marked as read");
    } else {
        header("Location: dashboard.php?error=Failed to mark message as read");
    }
} else {
    header("Location: dashboard.php");
}
?>
