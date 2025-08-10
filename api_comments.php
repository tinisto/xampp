<?php
// API endpoint for managing comments
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

$action = $_GET['action'] ?? '';

if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add new comment
    $itemType = $_POST['item_type'] ?? '';
    $itemId = intval($_POST['item_id'] ?? 0);
    $commentText = trim($_POST['comment_text'] ?? '');
    
    if (empty($itemType) || !$itemId || empty($commentText)) {
        echo json_encode(['error' => 'Все поля обязательны']);
        exit;
    }
    
    // Validate comment length
    if (strlen($commentText) > 1000) {
        echo json_encode(['error' => 'Комментарий слишком длинный']);
        exit;
    }
    
    // Insert comment
    $commentId = db_insert_id("
        INSERT INTO comments (user_id, item_type, item_id, comment_text)
        VALUES (?, ?, ?, ?)
    ", [$_SESSION['user_id'], $itemType, $itemId, $commentText]);
    
    if ($commentId) {
        // Send notification to content author if not commenting on own content
        require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/email.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/notifications.php';
        
        // Get item details and author
        $itemDetails = null;
        $authorEmail = null;
        
        if ($itemType === 'news') {
            $itemDetails = db_fetch_one("
                SELECT n.title_news as title, n.author_id, u.email, u.name
                FROM news n
                LEFT JOIN users u ON n.author_id = u.id
                WHERE n.id_news = ?
            ", [$itemId]);
        } elseif ($itemType === 'post') {
            $itemDetails = db_fetch_one("
                SELECT p.title_post as title, p.author_id, u.email, u.name
                FROM posts p
                LEFT JOIN users u ON p.author_id = u.id
                WHERE p.id = ?
            ", [$itemId]);
        }
        
        // Send notification if author exists and is not the commenter
        if ($itemDetails && $itemDetails['author_id'] && $itemDetails['author_id'] != $_SESSION['user_id']) {
            // Create in-app notification
            NotificationManager::notifyNewComment(
                $itemDetails['author_id'],
                $_SESSION['user_name'],
                $itemDetails['title'],
                $itemType,
                $itemId
            );
            
            // Send email notification if email exists
            if ($itemDetails['email']) {
                EmailNotification::sendCommentNotification(
                    $itemDetails['email'],
                    $itemDetails['name'],
                    $itemDetails['title'],
                    $_SESSION['user_name'],
                    $commentText
                );
            }
        }
        
        echo json_encode(['success' => true, 'comment_id' => $commentId]);
    } else {
        echo json_encode(['error' => 'Ошибка при добавлении комментария']);
    }
    
} elseif ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Delete comment
    $input = json_decode(file_get_contents('php://input'), true);
    $commentId = intval($input['id'] ?? 0);
    
    if (!$commentId) {
        echo json_encode(['error' => 'Invalid comment ID']);
        exit;
    }
    
    // Check if user owns the comment
    $comment = db_fetch_one("
        SELECT user_id FROM comments WHERE id = ?
    ", [$commentId]);
    
    if (!$comment || $comment['user_id'] != $_SESSION['user_id']) {
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }
    
    // Delete comment
    $success = db_execute("
        DELETE FROM comments WHERE id = ? AND user_id = ?
    ", [$commentId, $_SESSION['user_id']]);
    
    echo json_encode(['success' => $success]);
    
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request']);
}
?>