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

// Validate required fields
if (empty($entityType) || $entityId <= 0 || empty($author) || empty($comment)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Все обязательные поля должны быть заполнены']);
    exit;
}

// Validate comment length
if (strlen($comment) < 3) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Комментарий слишком короткий (минимум 3 символа)']);
    exit;
}

if (strlen($comment) > 2000) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Комментарий слишком длинный (максимум 2000 символов)']);
    exit;
}

// Validate author name
if (strlen($author) < 2) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Имя слишком короткое (минимум 2 символа)']);
    exit;
}

if (strlen($author) > 100) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Имя слишком длинное (максимум 100 символов)']);
    exit;
}

// Validate email if provided
if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Некорректный email адрес']);
    exit;
}

// Basic spam protection
$spamWords = ['spam', 'casino', 'viagra', 'porn', 'xxx', 'sex', 'bet', 'loan'];
$commentLower = mb_strtolower($comment);
foreach ($spamWords as $spamWord) {
    if (strpos($commentLower, $spamWord) !== false) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Комментарий содержит недопустимый контент']);
        exit;
    }
}

// Rate limiting - max 3 comments per minute per IP
$clientIP = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$rateLimitQuery = "SELECT COUNT(*) as recent_comments FROM comments WHERE author_ip = ? AND date >= DATE_SUB(NOW(), INTERVAL 1 MINUTE)";
$stmt = $connection->prepare($rateLimitQuery);
$stmt->bind_param("s", $clientIP);
$stmt->execute();
$recentComments = $stmt->get_result()->fetch_assoc()['recent_comments'];

if ($recentComments >= 3) {
    http_response_code(429);
    echo json_encode(['success' => false, 'error' => 'Слишком много комментариев. Подождите минуту перед добавлением следующего.']);
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