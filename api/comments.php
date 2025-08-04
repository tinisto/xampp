<?php
/**
 * Comments API Endpoints
 * Handle AJAX requests for comment functionality
 */

session_start();
require_once __DIR__ . '/../config/loadEnv.php';
require_once __DIR__ . '/../database/db_connections.php';
require_once __DIR__ . '/../includes/comments/comment_enhancements.php';
require_once __DIR__ . '/../includes/security/csrf.php';

// Set JSON response headers
header('Content-Type: application/json');
header('X-Robots-Tag: noindex');

// CORS headers for AJAX requests
header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_HOST']);
header('Access-Control-Allow-Methods: POST, GET');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

/**
 * Send JSON response and exit
 */
function sendResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Send error response
 */
function sendError($message, $statusCode = 400) {
    sendResponse(['success' => false, 'message' => $message], $statusCode);
}

// Get request method and action
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? $_POST['action'] ?? '';

// Validate CSRF token for POST requests
if ($method === 'POST') {
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!CSRFProtection::validateAndConsumeToken($token)) {
        sendError('Недействительный CSRF токен', 403);
    }
}

// Check if user is logged in for protected actions
$protectedActions = ['like', 'dislike', 'report', 'create', 'pin'];
if (in_array($action, $protectedActions) && !isset($_SESSION['user_id'])) {
    sendError('Необходима авторизация', 401);
}

// Route to appropriate handler
switch ($action) {
    case 'like':
    case 'dislike':
        handleReaction($action);
        break;
        
    case 'report':
        handleReport();
        break;
        
    case 'get':
        handleGet();
        break;
        
    case 'create':
        handleCreate();
        break;
        
    case 'pin':
        handlePin();
        break;
        
    case 'moderate':
        handleModerate();
        break;
        
    case 'stats':
        handleStats();
        break;
        
    default:
        sendError('Неизвестное действие', 400);
}

/**
 * Handle like/dislike reactions
 */
function handleReaction($action) {
    $commentId = (int)($_POST['comment_id'] ?? 0);
    $userId = $_SESSION['user_id'];
    
    if (!$commentId) {
        sendError('ID комментария не указан');
    }
    
    // Check if comment exists
    global $connection;
    $checkQuery = "SELECT id FROM comments WHERE id = ? AND approved = 1";
    $checkStmt = mysqli_prepare($connection, $checkQuery);
    mysqli_stmt_bind_param($checkStmt, 'i', $commentId);
    mysqli_stmt_execute($checkStmt);
    $result = mysqli_stmt_get_result($checkStmt);
    
    if (mysqli_num_rows($result) === 0) {
        sendError('Комментарий не найден');
    }
    
    $result = CommentEnhancements::toggleLike($commentId, $userId, $action);
    sendResponse($result);
}

/**
 * Handle comment reporting
 */
function handleReport() {
    $commentId = (int)($_POST['comment_id'] ?? 0);
    $reason = trim($_POST['reason'] ?? '');
    $userId = $_SESSION['user_id'];
    
    if (!$commentId) {
        sendError('ID комментария не указан');
    }
    
    if (empty($reason)) {
        sendError('Причина жалобы не указана');
    }
    
    if (strlen($reason) > 500) {
        sendError('Причина жалобы слишком длинная');
    }
    
    $result = CommentEnhancements::reportComment($commentId, $userId, $reason);
    sendResponse($result);
}

/**
 * Handle getting comments
 */
function handleGet() {
    $postId = (int)($_GET['post_id'] ?? 0);
    $userId = $_SESSION['user_id'] ?? null;
    
    if (!$postId) {
        sendError('ID поста не указан');
    }
    
    $options = [
        'include_reactions' => true,
        'order_by' => $_GET['order'] ?? 'pinned DESC, created_at ASC',
        'limit' => min((int)($_GET['limit'] ?? 50), 100)
    ];
    
    $comments = CommentEnhancements::getEnhancedComments($postId, $userId, $options);
    
    sendResponse([
        'success' => true,
        'comments' => $comments,
        'count' => count($comments)
    ]);
}

/**
 * Handle creating new comment
 */
function handleCreate() {
    $postId = (int)($_POST['post_id'] ?? 0);
    $content = trim($_POST['content'] ?? '');
    $parentId = (int)($_POST['parent_id'] ?? 0) ?: null;
    $userId = $_SESSION['user_id'];
    
    if (!$postId) {
        sendError('ID поста не указан');
    }
    
    if (empty($content)) {
        sendError('Содержимое комментария не может быть пустым');
    }
    
    if (strlen($content) > 2000) {
        sendError('Комментарий слишком длинный (максимум 2000 символов)');
    }
    
    // Check if post exists
    global $connection;
    $postQuery = "SELECT id FROM posts WHERE id = ? AND approved = 1";
    $postStmt = mysqli_prepare($connection, $postQuery);
    mysqli_stmt_bind_param($postStmt, 'i', $postId);
    mysqli_stmt_execute($postStmt);
    $postResult = mysqli_stmt_get_result($postStmt);
    
    if (mysqli_num_rows($postResult) === 0) {
        sendError('Пост не найден');
    }
    
    // Check parent comment if specified
    if ($parentId) {
        $parentQuery = "SELECT id FROM comments WHERE id = ? AND post_id = ? AND approved = 1";
        $parentStmt = mysqli_prepare($connection, $parentQuery);
        mysqli_stmt_bind_param($parentStmt, 'ii', $parentId, $postId);
        mysqli_stmt_execute($parentStmt);
        $parentResult = mysqli_stmt_get_result($parentStmt);
        
        if (mysqli_num_rows($parentResult) === 0) {
            sendError('Родительский комментарий не найден');
        }
    }
    
    // Rate limiting: max 3 comments per minute
    $rateLimitQuery = "SELECT COUNT(*) as count FROM comments 
                       WHERE user_id = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 MINUTE)";
    $rateLimitStmt = mysqli_prepare($connection, $rateLimitQuery);
    mysqli_stmt_bind_param($rateLimitStmt, 'i', $userId);
    mysqli_stmt_execute($rateLimitStmt);
    $rateLimitResult = mysqli_stmt_get_result($rateLimitStmt);
    $rateLimitData = mysqli_fetch_assoc($rateLimitResult);
    
    if ($rateLimitData['count'] >= 3) {
        sendError('Слишком много комментариев. Подождите минуту.');
    }
    
    // Insert comment
    $insertQuery = "INSERT INTO comments (post_id, user_id, parent_id, content, created_at, approved) 
                    VALUES (?, ?, ?, ?, NOW(), 1)";
    $insertStmt = mysqli_prepare($connection, $insertQuery);
    mysqli_stmt_bind_param($insertStmt, 'iiis', $postId, $userId, $parentId, $content);
    
    if (mysqli_stmt_execute($insertStmt)) {
        $commentId = mysqli_insert_id($connection);
        
        // Get the created comment with user data
        $getCommentQuery = "SELECT c.*, u.username, u.avatar 
                           FROM comments c 
                           JOIN users u ON c.user_id = u.id 
                           WHERE c.id = ?";
        $getCommentStmt = mysqli_prepare($connection, $getCommentQuery);
        mysqli_stmt_bind_param($getCommentStmt, 'i', $commentId);
        mysqli_stmt_execute($getCommentStmt);
        $commentResult = mysqli_stmt_get_result($getCommentStmt);
        $comment = mysqli_fetch_assoc($commentResult);
        
        // Format the comment
        $comment['content_html'] = CommentEnhancements::processCommentContent($comment['content']);
        $comment['created_at_formatted'] = 'только что';
        $comment['likes'] = 0;
        $comment['dislikes'] = 0;
        $comment['user_reaction'] = null;
        $comment['is_recent'] = true;
        
        sendResponse([
            'success' => true,
            'message' => 'Комментарий добавлен',
            'comment' => $comment
        ]);
    } else {
        sendError('Ошибка при добавлении комментария');
    }
}

/**
 * Handle pinning/unpinning comments (admin only)
 */
function handlePin() {
    // Check if user is admin
    if ($_SESSION['role'] !== 'admin') {
        sendError('Недостаточно прав', 403);
    }
    
    $commentId = (int)($_POST['comment_id'] ?? 0);
    $pinned = (bool)($_POST['pinned'] ?? false);
    
    if (!$commentId) {
        sendError('ID комментария не указан');
    }
    
    if (CommentEnhancements::togglePin($commentId, $pinned)) {
        sendResponse([
            'success' => true,
            'message' => $pinned ? 'Комментарий закреплен' : 'Комментарий откреплен',
            'pinned' => $pinned
        ]);
    } else {
        sendError('Ошибка при изменении статуса закрепления');
    }
}

/**
 * Handle comment moderation (admin only)
 */
function handleModerate() {
    // Check if user is admin
    if ($_SESSION['role'] !== 'admin') {
        sendError('Недостаточно прав', 403);
    }
    
    $commentId = (int)($_POST['comment_id'] ?? 0);
    $moderationAction = $_POST['moderation_action'] ?? '';
    $moderatorId = $_SESSION['user_id'];
    
    if (!$commentId) {
        sendError('ID комментария не указан');
    }
    
    if (!in_array($moderationAction, ['approve', 'reject', 'delete'])) {
        sendError('Неизвестное действие модерации');
    }
    
    if (CommentEnhancements::moderateComment($commentId, $moderationAction, $moderatorId)) {
        $messages = [
            'approve' => 'Комментарий одобрен',
            'reject' => 'Комментарий отклонен',
            'delete' => 'Комментарий удален'
        ];
        
        sendResponse([
            'success' => true,
            'message' => $messages[$moderationAction]
        ]);
    } else {
        sendError('Ошибка при модерации комментария');
    }
}

/**
 * Handle getting comment statistics (admin only)
 */
function handleStats() {
    // Check if user is admin
    if ($_SESSION['role'] !== 'admin') {
        sendError('Недостаточно прав', 403);
    }
    
    $stats = CommentEnhancements::getCommentStats();
    
    // Get comments for moderation
    $moderationComments = CommentEnhancements::getCommentsForModeration(10);
    
    sendResponse([
        'success' => true,
        'stats' => $stats,
        'moderation_queue' => $moderationComments
    ]);
}
?>