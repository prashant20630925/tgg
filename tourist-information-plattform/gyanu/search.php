</p>
<?php
// Simple, secure search page
require_once 'auth_check.php';
require_once 'config.php';

$query = trim($_GET['query'] ?? '');

// Prepare and execute safely
$rows = [];
if ($query !== '') {
        $like = "%{$query}%";
        $stmt = $conn->prepare("SELECT id, name, location, description, image FROM destinations WHERE name LIKE ? OR location LIKE ? LIMIT 50");
        if ($stmt) {
                $stmt->bind_param('ss', $like, $like);
                $stmt->execute();
                $res = $stmt->get_result();
                while ($r = $res->fetch_assoc()) { $rows[] = $r; }
                $stmt->close();
        }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Search Results</title>
    <link rel="stylesheet" href="css/simple.css">
    <style> .result { margin-bottom:18px; padding:12px; background:#fff; border-radius:8px; box-shadow:0 6px 18px rgba(2,6,23,0.04);} .nores{color:#6b7280} </style>
</head>
<body style="padding:20px; background:var(--bg); font-family:Inter, Arial, sans-serif;">
    <div style="max-width:1000px;margin:20px auto;">
        <h2>Search Results for "<?php echo htmlspecialchars($query); ?>"</h2>

        <?php if (empty($query)): ?>
            <p class="nores">Please enter a search term.</p>
        <?php elseif (empty($rows)): ?>
            <p class="nores">No destination found.</p>
        <?php else: ?>
            <?php foreach ($rows as $row): ?>
                <div class="result">
                    <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                    <?php if (!empty($row['image'])): ?><img src="./images/destination/<?php echo htmlspecialchars($row['image']); ?>" width="220" style="float:right;margin-left:14px;border-radius:6px;"><?php endif; ?>
                    <p style="margin:0 0 8px;"><strong>Location:</strong> <?php echo htmlspecialchars($row['location']); ?></p>
                    <p style="margin:0 0 8px;"><?php echo htmlspecialchars($row['description']); ?></p>
                    <a class="btn" href="destination-details.php?id=<?php echo (int)$row['id']; ?>">View</a>
                    <div style="clear:both"></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>
</body>
</html>
