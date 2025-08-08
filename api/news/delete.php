<?php
// News deletion API endpoint

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

// Extract ID from path like: /api/news/delete/622
for ($i = 0; $i < count($pathParts); $i++) {
    if ($pathParts[$i] === 'delete' && isset($pathParts[$i + 1])) {
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
    // Delete the news item
    $query = "DELETE FROM news WHERE id = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("i", $newsId);
    
    if ($stmt->execute()) {
        // Check if any rows were affected
        if ($stmt->affected_rows > 0) {
            $success = true;
            $message = "Новость успешно удалена";
        } else {
            $success = false;
            $message = "Новость не найдена";
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
    // Redirect with success/error message
    $status = $success ? 'success' : 'error';
    $redirectUrl = $redirect . (strpos($redirect, '?') !== false ? '&' : '?') . 
                   "action=delete&status={$status}&message=" . urlencode($message);
    header("Location: {$redirectUrl}");
    exit;
} else {
    // JSON response
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'id' => $newsId
    ]);
}
?>