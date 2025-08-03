<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db_connect.php';

// Check if user is logged in and has permission
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

// Check if news ID is provided
if (!isset($_POST['id_news']) || !is_numeric($_POST['id_news'])) {
    $_SESSION['error_message'] = 'Неверный ID новости.';
    header('Location: /news');
    exit;
}

$newsId = (int)$_POST['id_news'];
$userId = $_SESSION['user_id'];
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

try {
    // Check if the news exists and if user has permission to delete it
    $checkQuery = "SELECT user_id, title_news FROM news WHERE id_news = ?";
    $checkStmt = $connection->prepare($checkQuery);
    $checkStmt->bind_param("i", $newsId);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows === 0) {
        $_SESSION['error_message'] = 'Новость не найдена.';
        header('Location: /news');
        exit;
    }
    
    $newsData = $result->fetch_assoc();
    
    // Check permission - admin can delete any news, user can only delete their own
    if (!$isAdmin && $newsData['user_id'] != $userId) {
        $_SESSION['error_message'] = 'У вас нет прав для удаления этой новости.';
        header('Location: /news');
        exit;
    }
    
    // Delete associated images
    for ($i = 1; $i <= 3; $i++) {
        $imagePath = $_SERVER['DOCUMENT_ROOT'] . "/images/news-images/{$newsId}_{$i}.jpg";
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
    
    // Delete the news record
    $deleteQuery = "DELETE FROM news WHERE id_news = ?";
    $deleteStmt = $connection->prepare($deleteQuery);
    $deleteStmt->bind_param("i", $newsId);
    
    if ($deleteStmt->execute()) {
        $_SESSION['success_message'] = "Новость '{$newsData['title_news']}' успешно удалена.";
    } else {
        $_SESSION['error_message'] = 'Ошибка при удалении новости.';
    }
    
    $deleteStmt->close();
    $checkStmt->close();
    
} catch (Exception $e) {
    $_SESSION['error_message'] = 'Произошла ошибка при удалении новости.';
    error_log("News delete error: " . $e->getMessage());
}

header('Location: /news');
exit;
?>