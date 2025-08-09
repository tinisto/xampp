<?php
/**
 * Edit Comment API Endpoint
 * Allows users to edit their own comments within a time limit
 */

session_start();
header('Content-Type: application/json');

// Database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Validate input
$comment_id = (int)($input['comment_id'] ?? 0);
$new_text = trim($input['new_text'] ?? '');
$edit_reason = trim($input['edit_reason'] ?? '');
$user_id = (int)($_SESSION['user_id'] ?? 0);

// Validate comment text
if (strlen($new_text) < 3 || strlen($new_text) > 2000) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Комментарий должен быть от 3 до 2000 символов']);
    exit;
}

// Check if user is logged in
if ($user_id === 0) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Необходимо войти в систему для редактирования']);
    exit;
}

// Get comment details and check ownership
$stmt = $connection->prepare("SELECT id, user_id, comment_text, date, edit_count FROM comments WHERE id = ?");
$stmt->bind_param("i", $comment_id);
$stmt->execute();
$comment = $stmt->get_result()->fetch_assoc();

if (!$comment) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Комментарий не найден']);
    exit;
}

// Check if user owns the comment or is admin
$is_admin = ($_SESSION['role'] ?? '') === 'admin' || ($_SESSION['occupation'] ?? '') === 'admin';
if ($comment['user_id'] != $user_id && !$is_admin) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Вы можете редактировать только свои комментарии']);
    exit;
}

// Check edit time limit (15 minutes for regular users, no limit for admins)
$time_since_post = time() - strtotime($comment['date']);
$edit_time_limit = 15 * 60; // 15 minutes in seconds

if (!$is_admin && $time_since_post > $edit_time_limit) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Время редактирования истекло (15 минут)']);
    exit;
}

// Check edit count limit (3 edits for regular users, no limit for admins)
$edit_limit = 3;
if (!$is_admin && $comment['edit_count'] >= $edit_limit) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Достигнут лимит редактирований (3 раза)']);
    exit;
}

// Check if text actually changed
if ($comment['comment_text'] === $new_text) {
    echo json_encode(['success' => true, 'message' => 'Изменений не обнаружено']);
    exit;
}

// Start transaction
$connection->begin_transaction();

try {
    // Save edit history
    $stmt = $connection->prepare("INSERT INTO comment_edits (comment_id, user_id, old_text, new_text, edit_reason) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $comment_id, $user_id, $comment['comment_text'], $new_text, $edit_reason);
    $stmt->execute();
    
    // Update comment
    $stmt = $connection->prepare("UPDATE comments SET comment_text = ?, edited_at = NOW(), edit_count = edit_count + 1 WHERE id = ?");
    $stmt->bind_param("si", $new_text, $comment_id);
    $stmt->execute();
    
    // Get updated comment data
    $stmt = $connection->prepare("SELECT id, comment_text, edited_at, edit_count FROM comments WHERE id = ?");
    $stmt->bind_param("i", $comment_id);
    $stmt->execute();
    $updated_comment = $stmt->get_result()->fetch_assoc();
    
    $connection->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Комментарий успешно отредактирован',
        'comment' => [
            'id' => (int)$updated_comment['id'],
            'comment_text' => $updated_comment['comment_text'],
            'edited_at' => $updated_comment['edited_at'],
            'edit_count' => (int)$updated_comment['edit_count']
        ]
    ]);
    
} catch (Exception $e) {
    $connection->rollback();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Ошибка при сохранении изменений']);
}
?>