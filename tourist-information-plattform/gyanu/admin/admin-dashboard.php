<?php
session_start();

// Check for session timeout (30 minutes of inactivity)
$timeout_duration = 1800; // 30 minutes in seconds
if (isset($_SESSION['last_activity'])) {
    $elapsed = time() - $_SESSION['last_activity'];
    if ($elapsed > $timeout_duration) {
        // Session expired
        $_SESSION = [];
        session_destroy();
        header('Location: admin-login.php?expired=1');
        exit;
    }
}

// Block access if admin not logged in
if (!isset($_SESSION["admin"]) || empty($_SESSION["admin"])) {
    header("Location: admin-login.php");
    exit;
}

// Update last activity time
$_SESSION['last_activity'] = time();

// DB Connection
include "admin-config.php";
// get some counts for dashboard cards (guard against missing/failed DB connection or tables)
if (!isset($conn) || $conn->connect_error) {
    // DB not available — set default counts
    $users = ['total' => 0];
    $dest = ['total' => 0];
    $feedback = ['total' => 0];
} else {
    // Safely query each table with try-catch to handle missing tables
    try {
        $users_res = $conn->query("SELECT COUNT(*) AS total FROM users");
        $users = $users_res ? $users_res->fetch_assoc() : ['total' => 0];
    } catch (Exception $e) {
        $users = ['total' => 0];
    }
    
    try {
        $dest_res = $conn->query("SELECT COUNT(*) AS total FROM destinations");
        $dest = $dest_res ? $dest_res->fetch_assoc() : ['total' => 0];
    } catch (Exception $e) {
        $dest = ['total' => 0];
    }
    
    try {
        $feedback_res = $conn->query("SELECT COUNT(*) AS total FROM feedback");
        $feedback = $feedback_res ? $feedback_res->fetch_assoc() : ['total' => 0];
    } catch (Exception $e) {
        $feedback = ['total' => 0];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title>

<style>
/* ===== CSS VARIABLES (THEME) ===== */
:root {
    --primary-color: #1e73be;
    --primary-dark: #125a94;
    --success-color: #2ecc71;
    --info-color: #3498db;
    --warning-color: #f39c12;
    --danger-color: #e74c3c;
    --light-bg: #f4f6f9;
    --white: #ffffff;
    --dark-text: #333;
    --gray-text: #666;
    --border-color: #ddd;
    --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.1);
    --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.15);
    --shadow-lg: 0 8px 24px rgba(0, 0, 0, 0.2);
    --border-radius: 10px;
    --transition: all 0.3s ease;
}

/* ===== GLOBAL STYLES ===== */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: var(--light-bg);
    color: var(--dark-text);
}

/* ===== TOPBAR ===== */
.topbar {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    color: var(--white);
    padding: 16px 24px;
    font-size: 18px;
    font-weight: 600;
    box-shadow: var(--shadow-md);
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: sticky;
    top: 0;
    z-index: 100;
}

.topbar-title {
    display: flex;
    align-items: center;
    gap: 10px;
}

/* ===== SIDEBAR ===== */
.sidebar {
    width: 250px;
    height: calc(100vh - 60px);
    background: var(--white);
    border-right: 2px solid #e8ecf1;
    position: fixed;
    top: 60px;
    left: 0;
    overflow-y: auto;
    box-shadow: var(--shadow-sm);
}

.sidebar a {
    display: block;
    padding: 14px 20px;
    color: var(--gray-text);
    text-decoration: none;
    border-bottom: 1px solid #f0f0f0;
    transition: var(--transition);
    font-weight: 500;
    border-left: 4px solid transparent;
}

.sidebar a:hover {
    background: #f8f9fa;
    color: var(--primary-color);
    border-left-color: var(--primary-color);
    padding-left: 24px;
}

.sidebar a:first-child {
    border-left-color: var(--primary-color);
    background: #f8f9fa;
    color: var(--primary-color);
}

/* ===== MAIN CONTENT ===== */
.content {
    margin-left: 250px;
    padding: 30px;
    min-height: calc(100vh - 60px);
}

.content h2 {
    color: var(--primary-color);
    margin-bottom: 25px;
    font-size: 28px;
    font-weight: 700;
    letter-spacing: -0.5px;
}

/* ===== CARD GRID ===== */
.cardbox {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 24px;
    margin-bottom: 30px;
}

/* ===== CARDS ===== */
.card {
    background: var(--white);
    padding: 28px;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-sm);
    transition: var(--transition);
    border-top: 4px solid #e0e0e0;
    overflow: hidden;
    position: relative;
}

.card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--primary-color), var(--info-color));
}

.card:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-6px);
}

.card h2 {
    color: var(--dark-text);
    font-size: 18px;
    margin: 0 0 15px 0;
    text-transform: uppercase;
    letter-spacing: 1px;
    opacity: 0.8;
    font-weight: 600;
}

.value {
    font-size: 42px;
    font-weight: 800;
    color: var(--primary-color);
    margin: 15px 0;
    letter-spacing: -1px;
}

.card-subtitle {
    color: var(--gray-text);
    font-size: 13px;
    margin-bottom: 20px;
}

/* ===== BUTTONS ===== */
.btn {
    display: inline-block;
    padding: 12px 20px;
    margin-top: 15px;
    border-radius: 6px;
    text-decoration: none;
    color: var(--white);
    font-weight: 600;
    transition: var(--transition);
    border: none;
    cursor: pointer;
    font-size: 14px;
    letter-spacing: 0.5px;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.green {
    background: linear-gradient(135deg, var(--success-color) 0%, #27ae60 100%);
}

.blue {
    background: linear-gradient(135deg, var(--info-color) 0%, #2980b9 100%);
}

.orange {
    background: linear-gradient(135deg, var(--warning-color) 0%, #e67e22 100%);
}

/* ===== RESPONSIVE DESIGN ===== */
@media (max-width: 768px) {
    .sidebar {
        width: 200px;
    }

    .content {
        margin-left: 200px;
        padding: 20px;
    }

    .cardbox {
        grid-template-columns: 1fr;
        gap: 16px;
    }

    .topbar {
        padding: 12px 16px;
        font-size: 16px;
    }

    .value {
        font-size: 32px;
    }
}

@media (max-width: 600px) {
    .sidebar {
        width: 100%;
        height: auto;
        position: relative;
        top: 0;
        border-bottom: 2px solid #e8ecf1;
    }

    .content {
        margin-left: 0;
        padding: 16px;
    }

    .sidebar a {
        display: inline-block;
        padding: 12px 16px;
        border-bottom: none;
        border-right: 1px solid #f0f0f0;
    }

    .card {
        padding: 20px;
    }

    .value {
        font-size: 28px;
    }

    .content h2 {
        font-size: 22px;
        margin-bottom: 20px;
    }
}

/* ===== SCROLLBAR ===== */
.sidebar::-webkit-scrollbar {
    width: 6px;
}

.sidebar::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.sidebar::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

.sidebar::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>

</head>
<body>

<div class="topbar">
  <div class="topbar-title">
    🏢 Admin Dashboard — Welcome <strong><?php echo htmlspecialchars($_SESSION["admin"]); ?></strong>
  </div>
</div>

<div class="sidebar">
  <a href="admin-dashboard.php">🏠 Dashboard</a>
  <a href="destination-crud.php">🗺️ Manage Destinations</a>
  <a href="users-crud.php">👥 Manage Users</a>
  <a href="feedback-crud.php">💬 Manage Feedback</a>
  <a href="admin-logout.php">🚪 Logout</a>
</div>

<div class="content">

<h2>📊 System Overview</h2>

<div class="cardbox">

<div class="card">
  <h2>👥 Users</h2>
  <div class="value"><?php echo $users["total"]; ?></div>
  <p class="card-subtitle">Total registered users</p>
  <a class="btn blue" href="users-crud.php">View Users</a>
</div>

<div class="card">
  <h2>🗺️ Destinations</h2>
  <div class="value"><?php echo $dest["total"]; ?></div>
  <p class="card-subtitle">Total destinations</p>
  <a class="btn green" href="destination-crud.php">Manage Destinations</a>
</div>

<div class="card">
  <h2>💬 Feedback</h2>
  <div class="value"><?php echo $feedback["total"]; ?></div>
  <p class="card-subtitle">Total feedback received</p>
  <a class="btn orange" href="feedback-crud.php">View Feedback</a>
</div>

</div>

</div>

</body>
</html>


