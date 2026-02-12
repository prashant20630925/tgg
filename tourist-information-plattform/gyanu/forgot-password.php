<?php
session_start();
include "config.php";

$msg = '';
$msg_type = '';

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $msg = 'Security token validation failed. Please try again.';
        $msg_type = 'error';
    } else {
        $email = trim($_POST['email'] ?? '');

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $msg = 'Please enter a valid email address.';
            $msg_type = 'error';
        } else {
            // Check user exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows !== 1) {
                // For privacy, don't reveal whether email exists
                $msg = 'If an account exists for that email, a password reset link has been sent.';
                $msg_type = 'success';
            } else {
                // Ensure password_resets table exists
                $create_sql = "CREATE TABLE IF NOT EXISTS password_resets (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    email VARCHAR(255) NOT NULL,
                    token VARCHAR(128) NOT NULL,
                    expires_at INT NOT NULL,
                    created_at INT NOT NULL,
                    INDEX(token(64))
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
                $conn->query($create_sql);

                $token = bin2hex(random_bytes(32));
                $expires = time() + 3600; // 1 hour

                $ins = $conn->prepare("INSERT INTO password_resets (email, token, expires_at, created_at) VALUES (?, ?, ?, ?)");
                $now = time();
                $ins->bind_param('ssii', $email, $token, $expires, $now);
                $ins->execute();
                $ins->close();

                $reset_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http')
                    . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . "/reset-password.php?token=$token";

                // Attempt to send email
                $subject = 'Password reset request';
                $message = "We received a request to reset your password.\n\n";
                $message .= "If you requested this, visit the link below to reset your password (valid 1 hour):\n\n";
                $message .= $reset_link . "\n\n";
                $message .= "If you didn't request this, you can ignore this message.";
                $headers = 'From: noreply@' . $_SERVER['HTTP_HOST'] . "\r\n";

                $mail_sent = false;
                // Try mail() but fall back to showing the link (useful on local dev)
                if (function_exists('mail')) {
                    @mail($email, $subject, $message, $headers);
                    $mail_sent = true;
                }

                $msg_type = 'success';
                $msg = 'If an account exists for that email, a password reset link has been sent.';

                // For local/dev environments where mail isn't configured, also show the link once
                if (!$mail_sent) {
                    $msg .= ' Use this link to reset your password (one-time display):\n' . $reset_link;
                }
            }

            $stmt->close();
        }
    }
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Forgot Password</title>
  <link rel="stylesheet" href="css/auth.css">
</head>
<body>
  <div class="card">
    <h2>Forgot Password</h2>
    <p>Enter your email address and we'll send a link to reset your password.</p>

    <?php if (!empty($msg)): ?>
      <div class="notice <?php echo $msg_type === 'error' ? 'error' : 'success'; ?>">
        <?php echo nl2br(htmlspecialchars($msg)); ?>
      </div>
    <?php endif; ?>

    <form method="post" autocomplete="off">
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
      <label for="email">Email</label>
      <input id="email" name="email" type="email" required>
      <button type="submit">Send reset link</button>
    </form>

  </div>
</body>
</html>
