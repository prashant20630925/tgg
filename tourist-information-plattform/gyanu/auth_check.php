<?php


session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    // Destroy session and redirect to signin
    $_SESSION = [];
    session_destroy();
    header('Location: signin.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

// Optional: Check session timeout (30 minutes of inactivity)
$timeout_duration = 1800; // 30 minutes in seconds
if (isset($_SESSION['last_activity'])) {
    $elapsed = time() - $_SESSION['last_activity'];
    if ($elapsed > $timeout_duration) {
        // Session expired
        $_SESSION = [];
        session_destroy();
        header('Location: signin.php?expired=1');
        exit;
    }
}

// Update last activity time
$_SESSION['last_activity'] = time();

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Set secure cookie options
if (session_status() === PHP_SESSION_ACTIVE) {
    
}
?>
