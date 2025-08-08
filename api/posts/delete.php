<?php
// Posts deletion API endpoint

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check admin access
if ((!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') && 
    (!isset($_SESSION['occupation']) || $_SESSION['occupation'] !== 'admin')) {
    http_response_code(403);
    die('Access denied');
}

// Get post ID from URL path
$path = $_SERVER['REQUEST_URI'];
$pathParts = explode('/', trim($path, '/'));
$postId = null;

// Extract ID from path like: /api/posts/delete/123
for ($i = 0; $i < count($pathParts); $i++) {
    if ($pathParts[$i] === 'delete' && isset($pathParts[$i + 1])) {
        $postId = (int)$pathParts[$i + 1];
        break;
    }
}

if (!$postId) {
    http_response_code(400);
    die('Invalid post ID');
}

// Database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

try {
    // Delete the post
    $query = "DELETE FROM posts WHERE id_post = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("i", $postId);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $success = true;
            $message = "Статья успешно удалена";
        } else {
            $success = false;
            $message = "Статья не найдена";
        }
    } else {
        $success = false;
        $message = "Ошибка при удалении";
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    $success = false;
    $message = "Ошибка базы данных: " . $e->getMessage();
}

// Handle redirect or JSON response
$redirect = $_GET['redirect'] ?? '';

if ($redirect) {
    $status = $success ? 'success' : 'error';
    $redirectUrl = $redirect . (strpos($redirect, '?') !== false ? '&' : '?') . 
                   "action=delete&status={$status}&message=" . urlencode($message);
    header("Location: {$redirectUrl}");
    exit;
} else {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'id' => $postId
    ]);
}
?>