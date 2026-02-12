<?php
/**
 * Logout Handler
 * Safely destroys user session and redirects to signin page
 */

session_start();

// Store user info for logging (optional)
$logged_out_user = $_SESSION['user_email'] ?? 'Unknown';

// Clear all session variables
$_SESSION = [];

// Destroy the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect to signin page
header("Location: signin.php?logout=1");
exit;
?>
