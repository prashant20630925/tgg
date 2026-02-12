<?php
// One-off script to insert a new admin safely.
// Usage: run in browser or via CLI once, then remove or protect the file.

include __DIR__ . "/admin-config.php";

$username = 'MIRAJ';
$password_plain = '1234567890';

// Normalize username
$username = trim($username);

if (empty($username) || empty($password_plain)) {
    echo "Username or password empty\n";
    exit;
}

// Check if admins table exists and has a password column
$check = $conn->query("SHOW TABLES LIKE 'admins'");
if ($check->num_rows === 0) {
    echo "Table 'admins' does not exist.\n";
    exit;
}

// Check for existing user
$stmt = $conn->prepare("SELECT id FROM admins WHERE username = ? LIMIT 1");
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo "Admin with username '{$username}' already exists.\n";
    $stmt->close();
    exit;
}
$stmt->close();

// Hash password using password_hash
$hash = password_hash($password_plain, PASSWORD_DEFAULT);

$ins = $conn->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
if (!$ins) {
    echo "Prepare failed: " . $conn->error . "\n";
    exit;
}
$ins->bind_param('ss', $username, $hash);
if ($ins->execute()) {
    echo "Admin '{$username}' inserted successfully with id: " . $ins->insert_id . "\n";
} else {
    echo "Insert failed: " . $ins->error . "\n";
}
$ins->close();

// Important: remove or protect this file after use.
?>