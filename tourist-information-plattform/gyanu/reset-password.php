<?php
session_start();
include "config.php";

$msg = '';
$msg_type = '';

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$token = $_GET['token'] ?? $_POST['token'] ?? '';

if (empty($token)) {
    $msg = 'Invalid or missing token.';
    $msg_type = 'error';
} else {
    // Lookup token
    $stmt = $conn->prepare("SELECT email, expires_at FROM password_resets WHERE token = ? LIMIT 1");
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
        if ($row['expires_at'] < time()) {
            $msg = 'This reset link has expired.';
            $msg_type = 'error';
        } else {
            $email = $row['email'];

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                    $msg = 'Security token validation failed. Please try again.';
                    $msg_type = 'error';
                } else {
                    $password = $_POST['password'] ?? '';
                    $password2 = $_POST['password2'] ?? '';

                    if (empty($password) || strlen($password) < 8) {
                        $msg = 'Password must be at least 8 characters.';
                        $msg_type = 'error';
                    } elseif ($password !== $password2) {
                        $msg = 'Passwords do not match.';
                        $msg_type = 'error';
                    } else {
                        $hash = password_hash($password, PASSWORD_DEFAULT);
                        $up = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
                        $up->bind_param('ss', $hash, $email);
                        $up->execute();
                        $up->close();

                        // Delete used token
                        $del = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
                        $del->bind_param('s', $token);
                        $del->execute();
                        $del->close();

                        $msg = 'Password updated successfully. You may now sign in.';
                        $msg_type = 'success';
                    }
                }
            }
        }
    } else {
        $msg = 'Invalid reset token.';
        $msg_type = 'error';
    }

    $stmt->close();
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Reset Password</title>
  <link rel="stylesheet" href="css/auth.css">
</head>
<body>
  <div class="card">
    <h2>Reset Password</h2>

    <?php if (!empty($msg)): ?>
      <div class="notice <?php echo $msg_type === 'error' ? 'error' : 'success'; ?>">
        <?php echo htmlspecialchars($msg); ?>
      </div>
    <?php endif; ?>

    <?php if ($msg_type !== 'success'): ?>
      <form method="post" autocomplete="off">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

        <label for="password">New password</label>
        <input id="password" name="password" type="password" required>

        <label for="password2">Confirm password</label>
        <input id="password2" name="password2" type="password" required>

        <button type="submit">Set new password</button>
      </form>
    <?php else: ?>
      <p><a href="signin.php">Sign in</a></p>
    <?php endif; ?>

  </div>
</body>
</html>
