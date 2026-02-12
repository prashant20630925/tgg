<?php
// API for review and rating operations
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'add_review') {
    $destination_id = intval($_POST['destination_id'] ?? 0);
    $rating = intval($_POST['rating'] ?? 0);
    $review_text = trim($_POST['review_text'] ?? '');

    if ($destination_id <= 0 || $rating < 1 || $rating > 5) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input']);
        exit;
    }

    // Check if user already reviewed this destination
    $check_stmt = $conn->prepare("SELECT id FROM reviews WHERE destination_id = ? AND user_id = ?");
    $check_stmt->bind_param("ii", $destination_id, $user_id);
    $check_stmt->execute();
    $existing = $check_stmt->get_result()->fetch_assoc();
    $check_stmt->close();

    if ($existing) {
        // Update existing review
        $update_stmt = $conn->prepare("UPDATE reviews SET rating = ?, review_text = ? WHERE destination_id = ? AND user_id = ?");
        $update_stmt->bind_param("isii", $rating, $review_text, $destination_id, $user_id);
        if ($update_stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Review updated']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update review']);
        }
        $update_stmt->close();
    } else {
        // Insert new review
        $insert_stmt = $conn->prepare("INSERT INTO reviews (destination_id, user_id, rating, review_text) VALUES (?, ?, ?, ?)");
        $insert_stmt->bind_param("iiis", $destination_id, $user_id, $rating, $review_text);
        if ($insert_stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Review added']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to add review']);
        }
        $insert_stmt->close();
    }
    exit;
}

if ($action === 'get_reviews') {
    $destination_id = intval($_GET['destination_id'] ?? 0);

    if ($destination_id <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid destination']);
        exit;
    }

    $stmt = $conn->prepare("SELECT r.id, r.rating, r.review_text, r.created_at, u.fullname FROM reviews r 
                           JOIN users u ON r.user_id = u.id 
                           WHERE r.destination_id = ? 
                           ORDER BY r.created_at DESC");
    $stmt->bind_param("i", $destination_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $reviews = [];
    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }
    $stmt->close();

    echo json_encode(['reviews' => $reviews]);
    exit;
}

if ($action === 'get_rating') {
    $destination_id = intval($_GET['destination_id'] ?? 0);

    if ($destination_id <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid destination']);
        exit;
    }

    $stmt = $conn->prepare("SELECT AVG(rating) AS avg_rating, COUNT(*) AS total_reviews FROM reviews WHERE destination_id = ?");
    $stmt->bind_param("i", $destination_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    echo json_encode([
        'avg_rating' => round($result['avg_rating'] ?? 0, 1),
        'total_reviews' => $result['total_reviews'] ?? 0
    ]);
    exit;
}

http_response_code(400);
echo json_encode(['error' => 'Invalid action']);
?>
