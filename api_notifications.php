<?php
// Notifications API endpoint
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_modern.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/notifications.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Необходима авторизация']);
    exit;
}

$action = $_GET['action'] ?? '';

if ($action === 'check' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    // Check for new notifications
    $unreadCount = NotificationManager::getUnreadCount($_SESSION['user_id']);
    $recent = NotificationManager::getRecent($_SESSION['user_id'], 5);
    
    echo json_encode([
        'success' => true,
        'total_unread' => $unreadCount,
        'recent' => $recent,
        'new_count' => 0 // Would need to track last check time for this
    ]);
    
} elseif ($action === 'mark_read' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mark notification as read
    $input = json_decode(file_get_contents('php://input'), true);
    $notificationId = intval($input['notification_id'] ?? 0);
    
    if ($notificationId > 0) {
        $success = NotificationManager::markAsRead($notificationId, $_SESSION['user_id']);
        echo json_encode(['success' => $success]);
    } else {
        echo json_encode(['error' => 'Invalid notification ID']);
    }
    
} elseif ($action === 'mark_all_read' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mark all notifications as read
    $success = NotificationManager::markAllAsRead($_SESSION['user_id']);
    echo json_encode(['success' => $success]);
    
} elseif ($action === 'get_recent' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get recent notifications for dropdown
    $limit = intval($_GET['limit'] ?? 10);
    $notifications = NotificationManager::getRecent($_SESSION['user_id'], $limit);
    $unreadCount = NotificationManager::getUnreadCount($_SESSION['user_id']);
    
    echo json_encode([
        'success' => true,
        'notifications' => $notifications,
        'unread_count' => $unreadCount
    ]);
    
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request']);
}
?>