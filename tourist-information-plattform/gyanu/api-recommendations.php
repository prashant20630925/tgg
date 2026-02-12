<?php
// API for destination recommendations
session_start();
include "config.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated. Please login first.']);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

if (empty($action)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid action']);
    exit;
}

if ($action === 'get_recommended') {
    $user_id = $_SESSION['user_id'];
    $limit = intval($_GET['limit'] ?? 6);

    // Simple recommendation: destinations with highest average rating
    // Advanced: could factor in user's wishlist, reviews, similar categories
    
    $query = "SELECT d.*, 
                     COALESCE(AVG(r.rating), 0) as avg_rating,
                     COALESCE(COUNT(r.id), 0) as total_reviews
              FROM destinations d
              LEFT JOIN reviews r ON d.id = r.destination_id
              GROUP BY d.id
              ORDER BY avg_rating DESC, total_reviews DESC
              LIMIT ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $recommendations = [];
    while ($row = $result->fetch_assoc()) {
        $recommendations[] = $row;
    }
    $stmt->close();

    echo json_encode(['recommendations' => $recommendations]);
    exit;
}

if ($action === 'get_similar') {
    $destination_id = intval($_GET['destination_id'] ?? 0);
    $limit = intval($_GET['limit'] ?? 4);

    if ($destination_id <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid destination']);
        exit;
    }

    // Get the current destination's details (could add category matching)
    $stmt = $conn->prepare("SELECT * FROM destinations WHERE id = ?");
    $stmt->bind_param("i", $destination_id);
    $stmt->execute();
    $current = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$current) {
        http_response_code(404);
        echo json_encode(['error' => 'Destination not found']);
        exit;
    }

    // Get similar destinations (same location or high ratings)
    $query = "SELECT d.*, 
                     COALESCE(AVG(r.rating), 0) as avg_rating
              FROM destinations d
              LEFT JOIN reviews r ON d.id = r.destination_id
              WHERE d.id != ?
              GROUP BY d.id
              ORDER BY COALESCE(AVG(r.rating), 0) DESC
              LIMIT ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $destination_id, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $similar = [];
    while ($row = $result->fetch_assoc()) {
        $similar[] = $row;
    }
    $stmt->close();

    echo json_encode(['similar' => $similar]);
    exit;
}

http_response_code(400);
echo json_encode(['error' => 'Invalid action']);
?>
