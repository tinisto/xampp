<?php
// API endpoint for managing favorites
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_modern.php';

// Set JSON response header
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Get request data
$input = json_decode(file_get_contents('php://input'), true);
$action = $_GET['action'] ?? '';

if ($action === 'toggle' && isset($input['type']) && isset($input['id'])) {
    $type = $input['type'];
    $itemId = intval($input['id']);
    $userId = $_SESSION['user_id'];
    
    // Check if already favorited
    $existing = db_fetch_one("
        SELECT id FROM favorites 
        WHERE user_id = ? AND item_type = ? AND item_id = ?
    ", [$userId, $type, $itemId]);
    
    if ($existing) {
        // Remove from favorites
        db_execute("
            DELETE FROM favorites 
            WHERE user_id = ? AND item_type = ? AND item_id = ?
        ", [$userId, $type, $itemId]);
        
        echo json_encode(['status' => 'removed']);
    } else {
        // Add to favorites
        db_execute("
            INSERT INTO favorites (user_id, item_type, item_id) 
            VALUES (?, ?, ?)
        ", [$userId, $type, $itemId]);
        
        echo json_encode(['status' => 'added']);
    }
} elseif ($action === 'remove' && isset($input['type']) && isset($input['id'])) {
    $type = $input['type'];
    $itemId = intval($input['id']);
    $userId = $_SESSION['user_id'];
    
    // Remove from favorites
    db_execute("
        DELETE FROM favorites 
        WHERE user_id = ? AND item_type = ? AND item_id = ?
    ", [$userId, $type, $itemId]);
    
    echo json_encode(['status' => 'removed']);
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request']);
}
?>