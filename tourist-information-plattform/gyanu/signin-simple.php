<?php
session_start();
include "config.php";

$msg = "";
$msg_class = "";

// If already logged in, go to home
if (isset($_SESSION['user_id'])) {
    header("Location: mainpage.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $msg = "Email and password required!";
        $msg_class = "error";
    } else {
        // Find user
        $result = $conn->query("SELECT id, fullname, password FROM users WHERE email = '$email'");
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['fullname'];
                $_SESSION['user_email'] = $email;
                
                $msg = "Login successful! Redirecting...";
                $msg_class = "success";
                header("refresh:1;url=mainpage.php");
            } else {
                $msg = "Wrong password!";
                $msg_class = "error";
            }
        } else {
            $msg = "Email not found!";
            $msg_class = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    <link rel="stylesheet" href="css/simple.css">
</head>
<body>
    <div class="simple-card">
        <h1>Sign In</h1>
        <p class="subtitle">Welcome back — sign in to continue</p>

        <?php if (!empty($msg)): ?>
            <div class="msg <?php echo $msg_class; ?>">
                <?php echo htmlspecialchars($msg); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button class="primary" type="submit">Sign In</button>
        </form>

        <div class="footer">
            Don't have account? <a href="register-simple.php">Register here</a>
        </div>
    </div>
</body>
</html>
