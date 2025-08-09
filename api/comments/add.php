<?php
/**
 * API endpoint for adding comments and replies
 * Supports both top-level comments and threaded replies
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Load dynamic configuration
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/comment-config.php';

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Get POST data
$entityType = trim($_POST['entity_type'] ?? '');
$entityId = (int)($_POST['entity_id'] ?? 0);
$author = trim($_POST['author'] ?? '');
$email = trim($_POST['email'] ?? '');
$comment = trim($_POST['comment'] ?? '');
$parentId = isset($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;

// Get client IP
$clientIP = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

// Validate required fields
if (empty($entityType) || $entityId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Неверные параметры запроса']);
    exit;
}

// Use dynamic validation
$validation = validateComment($connection, $comment, $author, $email, $clientIP);
if (!$validation['success']) {
    http_response_code(400);
    echo json_encode($validation);
    exit;
}

// If this is a reply, validate parent comment exists
if ($parentId) {
    $parentQuery = "SELECT id, entity_type, entity_id FROM comments WHERE id = ?";
    $stmt = $connection->prepare($parentQuery);
    $stmt->bind_param("i", $parentId);
    $stmt->execute();
    $parentResult = $stmt->get_result();
    
    if ($parentResult->num_rows === 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Родительский комментарий не найден']);
        exit;
    }
    
    $parentComment = $parentResult->fetch_assoc();
    
    // Verify parent comment belongs to same entity
    if ($parentComment['entity_type'] !== $entityType || $parentComment['entity_id'] != $entityId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Некорректная ссылка на родительский комментарий']);
        exit;
    }
}

try {
    // Get user ID from session if available
    $userId = $_SESSION['user_id'] ?? null;
    
    // Prepare insert query
    $insertQuery = "INSERT INTO comments (entity_type, entity_id, user_id, author_of_comment, email, comment_text, parent_id, author_ip, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $connection->prepare($insertQuery);
    $stmt->bind_param("siissssi", $entityType, $entityId, $userId, $author, $email, $comment, $parentId, $clientIP);
    
    if ($stmt->execute()) {
        $newCommentId = $connection->insert_id;
        
        // If this is a reply, create notification
        if ($parentId) {
            // Get parent comment details
            $parentQuery = "SELECT user_id, email, author_of_comment FROM comments WHERE id = ?";
            $stmt = $connection->prepare($parentQuery);
            $stmt->bind_param("i", $parentId);
            $stmt->execute();
            $parentComment = $stmt->get_result()->fetch_assoc();
            
            // Create notification if parent has email
            if ($parentComment && !empty($parentComment['email'])) {
                $notifyQuery = "INSERT INTO comment_notifications (user_id, email, comment_id, parent_comment_id, type) 
                               VALUES (?, ?, ?, ?, 'reply')";
                $stmt = $connection->prepare($notifyQuery);
                $parentUserId = $parentComment['user_id'] ?: null;
                $stmt->bind_param("isii", $parentUserId, $parentComment['email'], $newCommentId, $parentId);
                $stmt->execute(); // Ignore errors - notifications are not critical
            }
        }
        
        // Check for @mentions and create notifications
        if (preg_match_all('/@(\w+)/', $comment, $matches)) {
            $mentionedUsers = array_unique($matches[1]);
            foreach ($mentionedUsers as $username) {
                // Find users by username (assuming first_name is used as username)
                $userQuery = "SELECT id, email FROM users WHERE first_name = ? AND email IS NOT NULL LIMIT 1";
                $stmt = $connection->prepare($userQuery);
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $mentionedUser = $stmt->get_result()->fetch_assoc();
                
                if ($mentionedUser) {
                    $notifyQuery = "INSERT INTO comment_notifications (user_id, email, comment_id, parent_comment_id, type) 
                                   VALUES (?, ?, ?, ?, 'mention')";
                    $stmt = $connection->prepare($notifyQuery);
                    $mentionParentId = $parentId ?: $newCommentId;
                    $stmt->bind_param("isii", $mentionedUser['id'], $mentionedUser['email'], $newCommentId, $mentionParentId);
                    $stmt->execute(); // Ignore errors
                }
            }
        }
        
        // Get the newly created comment for response
        $selectQuery = "SELECT id, author_of_comment, comment_text, date, parent_id FROM comments WHERE id = ?";
        $stmt = $connection->prepare($selectQuery);
        $stmt->bind_param("i", $newCommentId);
        $stmt->execute();
        $newComment = $stmt->get_result()->fetch_assoc();
        
        echo json_encode([
            'success' => true,
            'message' => $parentId ? 'Ответ успешно добавлен!' : 'Комментарий успешно добавлен!',
            'comment' => [
                'id' => (int)$newComment['id'],
                'author_of_comment' => $newComment['author_of_comment'],
                'comment_text' => $newComment['comment_text'],
                'date' => $newComment['date'],
                'parent_id' => $newComment['parent_id'] ? (int)$newComment['parent_id'] : null
            ]
        ]);
        
    } else {
        throw new Exception('Failed to insert comment: ' . $stmt->error);
    }
    
} catch (Exception $e) {
    error_log("Add comment API error: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Произошла ошибка при сохранении комментария. Попробуйте еще раз.',
        'debug' => $e->getMessage() // Remove in production
    ]);
}
?>