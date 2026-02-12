<?php
// API for wishlist operations
session_start();
include "config.php";

header('Content-Type: application/json');

// Check user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated. Please login first.']);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$user_id = $_SESSION['user_id'];

if (empty($action)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid action']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'add') {
    $destination_id = intval($_POST['destination_id'] ?? 0);

    if ($destination_id <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid destination']);
        exit;
    }

    $stmt = $conn->prepare("INSERT IGNORE INTO wishlist (user_id, destination_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $destination_id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Added to wishlist']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to add to wishlist']);
    }
    $stmt->close();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'remove') {
    $destination_id = intval($_POST['destination_id'] ?? 0);

    if ($destination_id <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid destination']);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND destination_id = ?");
    $stmt->bind_param("ii", $user_id, $destination_id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Removed from wishlist']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to remove from wishlist']);
    }
    $stmt->close();
    exit;
}

if ($action === 'check') {
    $destination_id = intval($_GET['destination_id'] ?? 0);

    if ($destination_id <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid destination']);
        exit;
    }

    $stmt = $conn->prepare("SELECT id FROM wishlist WHERE user_id = ? AND destination_id = ?");
    $stmt->bind_param("ii", $user_id, $destination_id);
    $stmt->execute();
    $exists = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    echo json_encode(['in_wishlist' => (bool)$exists]);
    exit;
}

if ($action === 'get_all') {
    $stmt = $conn->prepare("SELECT d.* FROM wishlist w 
                           JOIN destinations d ON w.destination_id = d.id 
                           WHERE w.user_id = ? 
                           ORDER BY w.added_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $wishlist = [];
    while ($row = $result->fetch_assoc()) {
        $wishlist[] = $row;
    }
    $stmt->close();

    echo json_encode(['wishlist' => $wishlist]);
    exit;
}

http_response_code(400);
echo json_encode(['error' => 'Invalid action']);
?>
