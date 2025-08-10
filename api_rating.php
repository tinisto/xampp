<?php
// Rating API endpoint
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_modern.php';

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

if ($action === 'submit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    $itemType = $input['item_type'] ?? '';
    $itemId = intval($input['item_id'] ?? 0);
    $rating = intval($input['rating'] ?? 0);
    
    // Validate input
    if (!in_array($itemType, ['news', 'post', 'vpo', 'spo', 'school'])) {
        echo json_encode(['error' => 'Неверный тип материала']);
        exit;
    }
    
    if ($itemId <= 0) {
        echo json_encode(['error' => 'Неверный ID материала']);
        exit;
    }
    
    if ($rating < 1 || $rating > 5) {
        echo json_encode(['error' => 'Оценка должна быть от 1 до 5']);
        exit;
    }
    
    // Check if user already rated this item
    $existingRating = db_fetch_one("
        SELECT id FROM ratings 
        WHERE user_id = ? AND item_type = ? AND item_id = ?
    ", [$_SESSION['user_id'], $itemType, $itemId]);
    
    if ($existingRating) {
        // Update existing rating
        $success = db_execute("
            UPDATE ratings 
            SET rating = ?, updated_at = datetime('now')
            WHERE user_id = ? AND item_type = ? AND item_id = ?
        ", [$rating, $_SESSION['user_id'], $itemType, $itemId]);
    } else {
        // Insert new rating
        $ratingId = db_insert_id("
            INSERT INTO ratings (user_id, item_type, item_id, rating)
            VALUES (?, ?, ?, ?)
        ", [$_SESSION['user_id'], $itemType, $itemId, $rating]);
        
        $success = $ratingId > 0;
    }
    
    if ($success) {
        // Send notification to content author
        require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/notifications.php';
        
        // Get item details and author
        $itemDetails = null;
        if ($itemType === 'news') {
            $itemDetails = db_fetch_one("
                SELECT n.title_news as title, n.author_id
                FROM news n
                WHERE n.id_news = ?
            ", [$itemId]);
        } elseif ($itemType === 'post') {
            $itemDetails = db_fetch_one("
                SELECT p.title_post as title, p.author_id
                FROM posts p
                WHERE p.id = ?
            ", [$itemId]);
        }
        
        // Notify author if exists and is not the rater
        if ($itemDetails && $itemDetails['author_id'] && $itemDetails['author_id'] != $_SESSION['user_id']) {
            NotificationManager::notifyNewRating(
                $itemDetails['author_id'],
                $_SESSION['user_name'],
                $itemDetails['title'],
                $rating,
                $itemType,
                $itemId
            );
        }
        
        // Get updated stats
        $stats = db_fetch_one("
            SELECT COUNT(*) as total, AVG(rating) as average
            FROM ratings
            WHERE item_type = ? AND item_id = ?
        ", [$itemType, $itemId]);
        
        echo json_encode([
            'success' => true,
            'rating' => $rating,
            'stats' => [
                'total' => intval($stats['total']),
                'average' => round($stats['average'], 1)
            ]
        ]);
    } else {
        echo json_encode(['error' => 'Ошибка при сохранении оценки']);
    }
    
} elseif ($action === 'get' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get rating stats
    $itemType = $_GET['item_type'] ?? '';
    $itemId = intval($_GET['item_id'] ?? 0);
    
    if (!$itemType || !$itemId) {
        echo json_encode(['error' => 'Неверные параметры']);
        exit;
    }
    
    // Get stats
    $stats = db_fetch_one("
        SELECT 
            COUNT(*) as total_ratings,
            AVG(rating) as average_rating
        FROM ratings
        WHERE item_type = ? AND item_id = ?
    ", [$itemType, $itemId]);
    
    // Get user's rating if logged in
    $userRating = null;
    if (isset($_SESSION['user_id'])) {
        $userRating = db_fetch_column("
            SELECT rating FROM ratings 
            WHERE item_type = ? AND item_id = ? AND user_id = ?
        ", [$itemType, $itemId, $_SESSION['user_id']]);
    }
    
    echo json_encode([
        'success' => true,
        'stats' => [
            'total' => intval($stats['total_ratings']),
            'average' => round($stats['average_rating'], 1)
        ],
        'user_rating' => $userRating
    ]);
    
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request']);
}
?>