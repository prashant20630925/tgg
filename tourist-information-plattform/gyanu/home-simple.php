<?php
session_start();

// If not logged in, send to signin page
if (!isset($_SESSION['user_id'])) {
    header("Location: signin-simple.php");
    exit;
}

// Redirect merged simple home to the main page
header('Location: mainpage.php');
exit;

