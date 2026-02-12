<?php
session_start();

// Block access if admin not logged in
if (!isset($_SESSION['admin']) || empty($_SESSION['admin'])) {
    header('Location: admin-login.php');
    exit;
}

include "admin-config.php";

$id = $_GET['id'];

// get image name
$result = $conn->query("SELECT image FROM destinations WHERE id=$id");
$data = $result->fetch_assoc();

// delete image file
if(file_exists("../images/destination/".$data['image'])) {
    unlink("../images/destination/".$data['image']);
}

// delete record
$conn->query("DELETE FROM destinations WHERE id=$id");

header("Location: destination-crud.php");
exit();
