<?php
// Database Structure Audit
$host = "localhost";
$user = "root";
$pass = "";
$db = "tguidee";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "=== DATABASE STRUCTURE AUDIT ===\n\n";

// Get all tables
$result = $conn->query("SHOW TABLES");
echo "TABLES IN DATABASE:\n";
$tables = [];
while ($row = $result->fetch_row()) {
    $tables[] = $row[0];
    echo "  - " . $row[0] . "\n";
}
echo "\n";

// Detailed table structure
foreach ($tables as $table) {
    echo "TABLE: $table\n";
    echo "COLUMNS:\n";
    $cols = $conn->query("DESCRIBE $table");
    while ($col = $cols->fetch_assoc()) {
        echo "  - {$col['Field']}: {$col['Type']} (NULL: {$col['Null']}, Key: {$col['Key']}, Default: {$col['Default']})\n";
    }
    
    // Count records
    $count = $conn->query("SELECT COUNT(*) as cnt FROM $table");
    $row = $count->fetch_assoc();
    echo "  RECORDS: {$row['cnt']}\n";
    echo "\n";
}

$conn->close();
?>
