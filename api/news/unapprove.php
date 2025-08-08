<?php
// News unapproval API endpoint

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

// Get news ID from URL path
$path = $_SERVER['REQUEST_URI'];
$pathParts = explode('/', trim($path, '/'));
$newsId = null;

// Extract ID from path
for ($i = 0; $i < count($pathParts); $i++) {
    if ($pathParts[$i] === 'unapprove' && isset($pathParts[$i + 1])) {
        $newsId = (int)$pathParts[$i + 1];
        break;
    }
}

if (!$newsId) {
    http_response_code(400);
    die('Invalid news ID');
}

// Database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

try {
    // Unapprove the news item
    $query = "UPDATE news SET approved = 0 WHERE id = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("i", $newsId);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $success = true;
            $message = "Новость снята с публикации";
        } else {
            $success = false;
            $message = "Новость не найдена";
        }
    } else {
        $success = false;
        $message = "Ошибка при снятии с публикации";
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
                   "action=unapprove&status={$status}&message=" . urlencode($message);
    header("Location: {$redirectUrl}");
    exit;
} else {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'id' => $newsId
    ]);
}
?>