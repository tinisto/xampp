<?php
session_start();

// Check admin access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: /login');
    exit();
}

// Include database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/config/environment.php';
if (!isset($connection)) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
}

// Get post ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    // Delete the post
    $sql = "DELETE FROM posts WHERE id_post = ?";
    $stmt = $connection->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Пост успешно удален!";
        } else {
            $_SESSION['error'] = "Ошибка при удалении поста: " . $stmt->error;
        }
        
        $stmt->close();
    } else {
        $_SESSION['error'] = "Ошибка подготовки запроса: " . $connection->error;
    }
} else {
    $_SESSION['error'] = "Неверный ID поста";
}

// Redirect back to posts management
header('Location: /dashboard/posts');
exit();
?>