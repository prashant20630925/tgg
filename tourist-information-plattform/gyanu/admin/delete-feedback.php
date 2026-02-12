<?php
session_start();

// Block access if admin not logged in
if (!isset($_SESSION['admin']) || empty($_SESSION['admin'])) {
    header('Location: admin-login.php');
    exit;
}

include "admin-config.php";

$id = $_GET['id'];

if ($id) {
    $stmt = $conn->prepare("DELETE FROM feedback WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header("Location: feedback-crud.php");
        exit();
    } else {
        echo "Error deleting feedback.";
    }
    $stmt->close();
} else {
    echo "Invalid feedback ID.";
}
?>
