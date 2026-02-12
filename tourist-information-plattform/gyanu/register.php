<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);

include "config.php";

$msg = "";
$msg_type = "";

// Redirect if already logged in
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    header("Location: mainpage.php");
    exit;
}

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verify CSRF token
    $token_valid = false;
    if (isset($_POST['csrf_token']) && isset($_SESSION['csrf_token'])) {
        $token_valid = hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
    }
    
    if (!$token_valid) {
        $msg = "Security token validation failed. Please try again.";
        $msg_type = "error";
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    } else {
        $fullname = trim($_POST['fullname'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm'] ?? '';

        // Validation
        if (empty($fullname) || empty($email) || empty($password) || empty($confirm)) {
            $msg = "All fields are required!";
            $msg_type = "error";
        }
        elseif (strlen($fullname) < 2) {
            $msg = "Full name must be at least 2 characters!";
            $msg_type = "error";
        }
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $msg = "Invalid email format!";
            $msg_type = "error";
        }
        elseif (strlen($password) < 8) {
            $msg = "Password must be at least 8 characters!";
            $msg_type = "error";
        }
        elseif ($password !== $confirm) {
            $msg = "Passwords do not match!";
            $msg_type = "error";
        }
        else {
            // Check if email exists
            $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
            if (!$check) {
                $msg = "Database error: " . $conn->error;
                $msg_type = "error";
            } else {
                $check->bind_param("s", $email);
                $check->execute();
                $check->store_result();

                if ($check->num_rows > 0) {
                    $msg = "Email already registered!";
                    $msg_type = "error";
                } else {
                    // Hash password
                    $hashed = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

                    // Insert user
                    $stmt = $conn->prepare("INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)");
                    if (!$stmt) {
                        $msg = "Database error: " . $conn->error;
                        $msg_type = "error";
                    } else {
                        $stmt->bind_param("sss", $fullname, $email, $hashed);

                        if ($stmt->execute()) {
                            $msg = "Account created successfully! Redirecting to login...";
                            $msg_type = "success";
                            $display_redirect = true;
                            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                        } else {
                            $msg = "Registration failed: " . $stmt->error;
                            $msg_type = "error";
                        }
                        $stmt->close();
                    }
                }
                $check->close();
            }
        }
    }
}
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Sign up</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
<style>
:root{
  --bg:#f6f7fb;
  --card:#ffffff;
  --muted:#7b8088;
}
*{box-sizing:border-box}
html,body{height:100%;}
body{
  margin:0;
  font-family:Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
  background:url('./images/explorenepal.jpg') no-repeat center center/cover;
  display:flex;
  align-items:center;
  justify-content:center;
  padding:32px;
  color:#111827;
  -webkit-backdrop-filter: blur(10px);
  backdrop-filter: blur(10px);
}
.card{
  width:100%;
  max-width:480px;
  background: rgba(255, 255, 255, 0.15);
  padding:36px 40px;
  border-radius:14px;
  box-shadow:0 10px 30px rgba(17,24,39,0.08);
  -webkit-backdrop-filter: blur(10px);
  backdrop-filter: blur(10px);
}
h1{margin:0 0 6px 0;font-size:32px;font-weight:700;text-align:center;}
.subtitle{text-align:center;color:var(--muted);margin-bottom:26px;font-weight:400;}
form{display:flex;flex-direction:column;gap:18px;}
label{display:block;font-size:13px;color:var(--muted);margin-bottom:8px;}
input[type="text"],
input[type="email"],
input[type="password"]{
  width:100%;
  padding:12px 14px;
  font-size:15px;
  border:0;
  border-bottom:2px solid #e6e6e9;
  outline:none;
  background:transparent;
  transition:all .15s ease-in-out;
}
input:focus{
  border-bottom-color:#cfcfe3;
  box-shadow:0 4px 12px rgba(99,102,241,0.06);
}
.password-row{display:flex;align-items:center;gap:8px;}
.toggle-btn{cursor:pointer;background:none;border:0;font-size:13px;color:var(--muted);padding:8px 6px;}
.btn{margin-top:6px;background:#0b0b0b;color:#fff;border:0;padding:14px 18px;border-radius:8px;font-size:16px;cursor:pointer;box-shadow:0 6px 18px rgba(11,11,11,0.08);}
.footer{text-align:center;margin-top:18px;color:var(--muted);font-size:14px;}
.footer a{color:#111827;font-weight:600;text-decoration:none;}
.alert{padding:12px 14px;margin-bottom:16px;border-radius:6px;font-size:14px;font-weight:500;}
.alert-error{background-color:#fee;color:#c33;border-left:4px solid #c33;}
.alert-success{background-color:#efe;color:#3c3;border-left:4px solid #3c3;}
@media (max-width:420px){.card{padding:26px;border-radius:12px;} h1{font-size:26px;}}
</style>
</head>
<body>

<main class="card">
  <h1>Registration form</h1>
  <p class="subtitle">Create your account</p>

  <?php
    if (!empty($msg)) {
        $alert_class = $msg_type === 'success' ? 'alert-success' : 'alert-error';
        echo "<p class='alert $alert_class'>" . htmlspecialchars($msg) . "</p>";
        if ($msg_type === 'success') {
            echo "<script>setTimeout(() => { window.location.href = 'signin.php'; }, 2000);</script>";
        }
    }
  ?>

  <form action="" method="post" id="signupForm" autocomplete="on">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
    
    <div class="field">
      <label for="fullname">Full Name</label>
      <input id="fullname" name="fullname" type="text" placeholder="Your full name" required>
    </div>

    <div class="field">
      <label for="email">Email Address</label>
      <input id="email" name="email" type="email" placeholder="you@example.com" required>
    </div>

    <div class="field">
      <label for="password">Password</label>
      <div class="password-row">
        <input id="password" name="password" type="password" placeholder="Minimum 8 characters" required>
        <button type="button" class="toggle-btn" id="toggle">Show</button>
      </div>
    </div>

    <div class="field">
      <label for="confirm">Confirm Password</label>
      <input id="confirm" name="confirm" type="password" placeholder="Re-enter password" required>
    </div>

    <button class="btn" type="submit">Sign up</button>
  </form>

  <p class="footer">
    Already have an account? <a href="signin.php">Sign in</a>.
  </p>
</main>

<script>
const pwd = document.getElementById('password');
const toggle = document.getElementById('toggle');
toggle.addEventListener('click', ()=>{
  if(pwd.type === 'password'){
    pwd.type = 'text';
    toggle.textContent = 'Hide';
  } else {
    pwd.type = 'password';
    toggle.textContent = 'Show';
  }
});
</script>

</body>
</html>


