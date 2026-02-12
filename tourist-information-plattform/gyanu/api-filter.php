<?php
// API for filtering and searching destinations
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

if ($action === 'filter') {
    $search = trim($_GET['search'] ?? '');
    $rating_min = floatval($_GET['rating_min'] ?? 0);
    $limit = intval($_GET['limit'] ?? 12);
    $offset = intval($_GET['offset'] ?? 0);

    $query = "SELECT d.*, 
                     COALESCE(AVG(r.rating), 0) as avg_rating,
                     COALESCE(COUNT(r.id), 0) as total_reviews
              FROM destinations d
              LEFT JOIN reviews r ON d.id = r.destination_id
              WHERE 1=1";

    $params = [];
    $param_types = '';

    // Search by name or description
    if (!empty($search)) {
        $search_term = "%$search%";
        $query .= " AND (d.name LIKE ? OR d.description LIKE ?)";
        $params[] = $search_term;
        $params[] = $search_term;
        $param_types .= 'ss';
    }

    $query .= " GROUP BY d.id";

    // Filter by minimum rating
    if ($rating_min > 0) {
        $query .= " HAVING AVG(r.rating) >= ?";
        $params[] = $rating_min;
        $param_types .= 'f';
    }

    $query .= " ORDER BY d.id DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    $param_types .= 'ii';

    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($param_types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    $destinations = [];
    while ($row = $result->fetch_assoc()) {
        $destinations[] = $row;
    }
    $stmt->close();

    echo json_encode(['destinations' => $destinations]);
    exit;
}

http_response_code(400);
echo json_encode(['error' => 'Invalid action']);
?>
