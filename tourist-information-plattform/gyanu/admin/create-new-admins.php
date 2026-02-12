<?php
// Script to create new admin accounts with generated credentials
// Run once, then delete or protect this file

include __DIR__ . "/admin-config.php";

// Generate secure random credentials for admins
$admins_to_create = [
    ['username' => 'admin1', 'password' => bin2hex(random_bytes(8))],
    ['username' => 'admin2', 'password' => bin2hex(random_bytes(8))],
    ['username' => 'admin3', 'password' => bin2hex(random_bytes(8))],
];

$created_admins = [];

foreach ($admins_to_create as $admin) {
    $username = trim($admin['username']);
    $password_plain = $admin['password'];

    if (empty($username) || empty($password_plain)) {
        echo "Skipping empty credential\n";
        continue;
    }

    // Check if admin already exists
    $check_stmt = $conn->prepare("SELECT id FROM admins WHERE username = ? LIMIT 1");
    $check_stmt->bind_param('s', $username);
    $check_stmt->execute();
    $check_stmt->store_result();
    
    if ($check_stmt->num_rows > 0) {
        echo "Admin '{$username}' already exists - skipping.\n";
        $check_stmt->close();
        continue;
    }
    $check_stmt->close();

    // Hash password using sha256 (matching admin-login.php logic)
    $hash = hash("sha256", $password_plain);

    // Insert new admin
    $ins_stmt = $conn->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
    if (!$ins_stmt) {
        echo "Prepare failed: " . $conn->error . "\n";
        continue;
    }
    
    $ins_stmt->bind_param('ss', $username, $hash);
    
    if ($ins_stmt->execute()) {
        $created_admins[] = [
            'username' => $username,
            'password' => $password_plain,
            'id' => $ins_stmt->insert_id
        ];
        echo "✓ Admin '{$username}' created successfully (ID: " . $ins_stmt->insert_id . ")\n";
    } else {
        echo "✗ Insert failed for '{$username}': " . $ins_stmt->error . "\n";
    }
    $ins_stmt->close();
}

$conn->close();

// Display credentials
echo "\n" . str_repeat("=", 50) . "\n";
echo "NEW ADMIN CREDENTIALS\n";
echo str_repeat("=", 50) . "\n";

if (count($created_admins) > 0) {
    foreach ($created_admins as $admin) {
        echo "\nUsername: {$admin['username']}\n";
        echo "Password: {$admin['password']}\n";
        echo "ID: {$admin['id']}\n";
    }
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Save these credentials in a secure location.\n";
    echo "DELETE this script after use for security.\n";
} else {
    echo "No new admins were created.\n";
}
?>