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

// Get news ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    // Delete the news item
    $sql = "DELETE FROM news WHERE id_news = ?";
    $stmt = $connection->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Новость успешно удалена!";
        } else {
            $_SESSION['error'] = "Ошибка при удалении новости: " . $stmt->error;
        }
        
        $stmt->close();
    } else {
        $_SESSION['error'] = "Ошибка подготовки запроса: " . $connection->error;
    }
} else {
    $_SESSION['error'] = "Неверный ID новости";
}

// Redirect back to news management
header('Location: /dashboard/news');
exit();
?>