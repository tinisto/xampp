<?php
/**
 * Like/Dislike API Endpoint
 * Handles likes and dislikes for comments
 */

header('Content-Type: application/json');

// Database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Validate input
$comment_id = (int)($input['comment_id'] ?? 0);
$action = $input['action'] ?? ''; // 'like' or 'dislike'
$user_id = (int)($_SESSION['user_id'] ?? 0);
$ip_address = $_SERVER['REMOTE_ADDR'];

// Validate action
if (!in_array($action, ['like', 'dislike'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid action']);
    exit;
}

// Validate comment exists
$stmt = $connection->prepare("SELECT id FROM comments WHERE id = ?");
$stmt->bind_param("i", $comment_id);
$stmt->execute();
if ($stmt->get_result()->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Comment not found']);
    exit;
}

// Check if user/IP has already voted
$check_vote = $user_id > 0 
    ? "SELECT * FROM comment_likes WHERE comment_id = ? AND user_id = ?"
    : "SELECT * FROM comment_likes WHERE comment_id = ? AND ip_address = ? AND user_id IS NULL";

$stmt = $connection->prepare($check_vote);
if ($user_id > 0) {
    $stmt->bind_param("ii", $comment_id, $user_id);
} else {
    $stmt->bind_param("is", $comment_id, $ip_address);
}
$stmt->execute();
$existing_vote = $stmt->get_result()->fetch_assoc();

$is_like = ($action === 'like') ? 1 : 0;

// Start transaction
$connection->begin_transaction();

try {
    if ($existing_vote) {
        // User has already voted
        if ($existing_vote['is_like'] == $is_like) {
            // Same vote - remove it (toggle off)
            $delete_query = $user_id > 0
                ? "DELETE FROM comment_likes WHERE comment_id = ? AND user_id = ?"
                : "DELETE FROM comment_likes WHERE comment_id = ? AND ip_address = ? AND user_id IS NULL";
            
            $stmt = $connection->prepare($delete_query);
            if ($user_id > 0) {
                $stmt->bind_param("ii", $comment_id, $user_id);
            } else {
                $stmt->bind_param("is", $comment_id, $ip_address);
            }
            $stmt->execute();
            
            // Update comment counts
            $column = $is_like ? 'likes' : 'dislikes';
            $stmt = $connection->prepare("UPDATE comments SET $column = GREATEST($column - 1, 0) WHERE id = ?");
            $stmt->bind_param("i", $comment_id);
            $stmt->execute();
            
            $action_type = 'removed';
        } else {
            // Different vote - update it
            $update_query = $user_id > 0
                ? "UPDATE comment_likes SET is_like = ? WHERE comment_id = ? AND user_id = ?"
                : "UPDATE comment_likes SET is_like = ? WHERE comment_id = ? AND ip_address = ? AND user_id IS NULL";
            
            $stmt = $connection->prepare($update_query);
            if ($user_id > 0) {
                $stmt->bind_param("iii", $is_like, $comment_id, $user_id);
            } else {
                $stmt->bind_param("iis", $is_like, $comment_id, $ip_address);
            }
            $stmt->execute();
            
            // Update comment counts (decrease old, increase new)
            $old_column = $is_like ? 'dislikes' : 'likes';
            $new_column = $is_like ? 'likes' : 'dislikes';
            
            $stmt = $connection->prepare("UPDATE comments SET $old_column = GREATEST($old_column - 1, 0), $new_column = $new_column + 1 WHERE id = ?");
            $stmt->bind_param("i", $comment_id);
            $stmt->execute();
            
            $action_type = 'changed';
        }
    } else {
        // New vote
        $insert_query = "INSERT INTO comment_likes (comment_id, user_id, ip_address, is_like) VALUES (?, ?, ?, ?)";
        $stmt = $connection->prepare($insert_query);
        $user_id_param = $user_id > 0 ? $user_id : null;
        $stmt->bind_param("iisi", $comment_id, $user_id_param, $ip_address, $is_like);
        $stmt->execute();
        
        // Update comment counts
        $column = $is_like ? 'likes' : 'dislikes';
        $stmt = $connection->prepare("UPDATE comments SET $column = $column + 1 WHERE id = ?");
        $stmt->bind_param("i", $comment_id);
        $stmt->execute();
        
        $action_type = 'added';
    }
    
    // Get updated counts
    $stmt = $connection->prepare("SELECT likes, dislikes FROM comments WHERE id = ?");
    $stmt->bind_param("i", $comment_id);
    $stmt->execute();
    $counts = $stmt->get_result()->fetch_assoc();
    
    $connection->commit();
    
    echo json_encode([
        'success' => true,
        'action_type' => $action_type,
        'likes' => (int)$counts['likes'],
        'dislikes' => (int)$counts['dislikes']
    ]);
    
} catch (Exception $e) {
    $connection->rollback();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database error']);
}
?>