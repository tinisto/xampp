<?php
/**
 * User interaction API handlers (favorites, ratings, comments, etc.)
 */

// Favorites handlers
function handleFavorites($segments, $method, $input, $user) {
    $endpoint = $segments[1] ?? '';
    
    switch ($endpoint) {
        case '':
            switch ($method) {
                case 'GET':
                    // Get user's favorites
                    $page = (int)($_GET['page'] ?? 1);
                    $limit = min((int)($_GET['limit'] ?? 20), 100);
                    $offset = ($page - 1) * $limit;
                    $type = $_GET['type'] ?? ''; // news, post, event
                    
                    $whereClauses = ['f.user_id = ?'];
                    $params = [$user['id']];
                    
                    if ($type) {
                        $whereClauses[] = 'f.item_type = ?';
                        $params[] = $type;
                    }
                    
                    $whereClause = implode(' AND ', $whereClauses);
                    
                    $favorites = db_fetch_all("
                        SELECT f.*, 
                               CASE 
                                   WHEN f.item_type = 'news' THEN n.title_news
                                   WHEN f.item_type = 'post' THEN p.title_post  
                                   WHEN f.item_type = 'event' THEN e.title
                               END as title,
                               CASE 
                                   WHEN f.item_type = 'news' THEN n.created_at
                                   WHEN f.item_type = 'post' THEN p.date_post
                                   WHEN f.item_type = 'event' THEN e.start_date
                               END as content_date
                        FROM favorites f
                        LEFT JOIN news n ON f.item_type = 'news' AND f.item_id = n.id_news
                        LEFT JOIN posts p ON f.item_type = 'post' AND f.item_id = p.id
                        LEFT JOIN events e ON f.item_type = 'event' AND f.item_id = e.id
                        WHERE $whereClause
                        ORDER BY f.created_at DESC
                        LIMIT $limit OFFSET $offset
                    ", $params);
                    
                    $total = db_fetch_column("SELECT COUNT(*) FROM favorites f WHERE $whereClause", $params);
                    
                    sendResponse(200, [
                        'success' => true,
                        'data' => $favorites,
                        'pagination' => [
                            'current_page' => $page,
                            'per_page' => $limit,
                            'total' => $total,
                            'total_pages' => ceil($total / $limit)
                        ]
                    ]);
                    break;
                    
                case 'POST':
                    // Add to favorites
                    $itemType = $input['item_type'] ?? '';
                    $itemId = (int)($input['item_id'] ?? 0);
                    
                    if (!$itemType || !$itemId) {
                        sendResponse(400, ['error' => 'Item type and ID required']);
                    }
                    
                    if (!in_array($itemType, ['news', 'post', 'event'])) {
                        sendResponse(400, ['error' => 'Invalid item type']);
                    }
                    
                    // Check if already favorited
                    $existing = db_fetch_one("
                        SELECT id FROM favorites 
                        WHERE user_id = ? AND item_type = ? AND item_id = ?
                    ", [$user['id'], $itemType, $itemId]);
                    
                    if ($existing) {
                        sendResponse(400, ['error' => 'Item already in favorites']);
                    }
                    
                    // Add to favorites
                    $result = db_query("
                        INSERT INTO favorites (user_id, item_type, item_id) 
                        VALUES (?, ?, ?)
                    ", [$user['id'], $itemType, $itemId]);
                    
                    if ($result) {
                        sendResponse(201, [
                            'success' => true,
                            'message' => 'Added to favorites'
                        ]);
                    } else {
                        sendResponse(500, ['error' => 'Failed to add to favorites']);
                    }
                    break;
                    
                case 'DELETE':
                    // Remove from favorites
                    $itemType = $input['item_type'] ?? '';
                    $itemId = (int)($input['item_id'] ?? 0);
                    
                    if (!$itemType || !$itemId) {
                        sendResponse(400, ['error' => 'Item type and ID required']);
                    }
                    
                    $result = db_query("
                        DELETE FROM favorites 
                        WHERE user_id = ? AND item_type = ? AND item_id = ?
                    ", [$user['id'], $itemType, $itemId]);
                    
                    sendResponse(200, [
                        'success' => true,
                        'message' => 'Removed from favorites'
                    ]);
                    break;
                    
                default:
                    sendResponse(405, ['error' => 'Method not allowed']);
            }
            break;
            
        default:
            sendResponse(404, ['error' => 'Favorites endpoint not found']);
    }
}

// Reading Lists handlers
function handleReadingLists($segments, $method, $input, $user) {
    $endpoint = $segments[1] ?? '';
    
    switch ($endpoint) {
        case '':
            switch ($method) {
                case 'GET':
                    // Get user's reading lists
                    $lists = db_fetch_all("
                        SELECT rl.*, 
                               COUNT(rli.id) as items_count,
                               COUNT(CASE WHEN rli.is_read = 1 THEN 1 END) as read_count
                        FROM reading_lists rl
                        LEFT JOIN reading_list_items rli ON rl.id = rli.list_id
                        WHERE rl.user_id = ?
                        GROUP BY rl.id
                        ORDER BY rl.created_at DESC
                    ", [$user['id']]);
                    
                    sendResponse(200, [
                        'success' => true,
                        'data' => $lists
                    ]);
                    break;
                    
                case 'POST':
                    // Create reading list
                    $name = $input['name'] ?? '';
                    $description = $input['description'] ?? '';
                    $isPublic = (int)($input['is_public'] ?? 0);
                    
                    if (!$name) {
                        sendResponse(400, ['error' => 'List name required']);
                    }
                    
                    $listId = db_insert_id("
                        INSERT INTO reading_lists (user_id, name, description, is_public) 
                        VALUES (?, ?, ?, ?)
                    ", [$user['id'], $name, $description, $isPublic]);
                    
                    if ($listId) {
                        sendResponse(201, [
                            'success' => true,
                            'message' => 'Reading list created',
                            'list_id' => $listId
                        ]);
                    } else {
                        sendResponse(500, ['error' => 'Failed to create reading list']);
                    }
                    break;
                    
                default:
                    sendResponse(405, ['error' => 'Method not allowed']);
            }
            break;
            
        default:
            // Handle specific list operations
            if (!is_numeric($endpoint)) {
                sendResponse(404, ['error' => 'Reading list not found']);
            }
            
            $listId = (int)$endpoint;
            $action = $segments[2] ?? '';
            
            // Verify list ownership
            $list = db_fetch_one("SELECT * FROM reading_lists WHERE id = ? AND user_id = ?", [$listId, $user['id']]);
            if (!$list) {
                sendResponse(404, ['error' => 'Reading list not found']);
            }
            
            switch ($action) {
                case '':
                    switch ($method) {
                        case 'GET':
                            // Get list items
                            $items = db_fetch_all("
                                SELECT rli.*,
                                       CASE 
                                           WHEN rli.item_type = 'news' THEN n.title_news
                                           WHEN rli.item_type = 'post' THEN p.title_post
                                           WHEN rli.item_type = 'event' THEN e.title
                                       END as title
                                FROM reading_list_items rli
                                LEFT JOIN news n ON rli.item_type = 'news' AND rli.item_id = n.id_news
                                LEFT JOIN posts p ON rli.item_type = 'post' AND rli.item_id = p.id
                                LEFT JOIN events e ON rli.item_type = 'event' AND rli.item_id = e.id
                                WHERE rli.list_id = ?
                                ORDER BY rli.added_at DESC
                            ", [$listId]);
                            
                            sendResponse(200, [
                                'success' => true,
                                'list' => $list,
                                'items' => $items
                            ]);
                            break;
                            
                        case 'PUT':
                            // Update list
                            $name = $input['name'] ?? $list['name'];
                            $description = $input['description'] ?? $list['description'];
                            $isPublic = isset($input['is_public']) ? (int)$input['is_public'] : $list['is_public'];
                            
                            $result = db_query("
                                UPDATE reading_lists 
                                SET name = ?, description = ?, is_public = ? 
                                WHERE id = ?
                            ", [$name, $description, $isPublic, $listId]);
                            
                            sendResponse(200, [
                                'success' => true,
                                'message' => 'Reading list updated'
                            ]);
                            break;
                            
                        case 'DELETE':
                            // Delete list
                            $result = db_query("DELETE FROM reading_lists WHERE id = ?", [$listId]);
                            
                            sendResponse(200, [
                                'success' => true,
                                'message' => 'Reading list deleted'
                            ]);
                            break;
                            
                        default:
                            sendResponse(405, ['error' => 'Method not allowed']);
                    }
                    break;
                    
                case 'items':
                    switch ($method) {
                        case 'POST':
                            // Add item to list
                            $itemType = $input['item_type'] ?? '';
                            $itemId = (int)($input['item_id'] ?? 0);
                            $notes = $input['notes'] ?? '';
                            
                            if (!$itemType || !$itemId) {
                                sendResponse(400, ['error' => 'Item type and ID required']);
                            }
                            
                            $result = db_query("
                                INSERT OR IGNORE INTO reading_list_items (list_id, item_type, item_id, notes) 
                                VALUES (?, ?, ?, ?)
                            ", [$listId, $itemType, $itemId, $notes]);
                            
                            sendResponse(201, [
                                'success' => true,
                                'message' => 'Item added to reading list'
                            ]);
                            break;
                            
                        case 'DELETE':
                            // Remove item from list
                            $itemType = $input['item_type'] ?? '';
                            $itemId = (int)($input['item_id'] ?? 0);
                            
                            $result = db_query("
                                DELETE FROM reading_list_items 
                                WHERE list_id = ? AND item_type = ? AND item_id = ?
                            ", [$listId, $itemType, $itemId]);
                            
                            sendResponse(200, [
                                'success' => true,
                                'message' => 'Item removed from reading list'
                            ]);
                            break;
                            
                        default:
                            sendResponse(405, ['error' => 'Method not allowed']);
                    }
                    break;
                    
                default:
                    sendResponse(404, ['error' => 'Reading list action not found']);
            }
    }
}

// Notifications handlers
function handleNotifications($segments, $method, $input, $user) {
    $endpoint = $segments[1] ?? '';
    
    switch ($endpoint) {
        case '':
            switch ($method) {
                case 'GET':
                    // Get notifications
                    $page = (int)($_GET['page'] ?? 1);
                    $limit = min((int)($_GET['limit'] ?? 20), 100);
                    $offset = ($page - 1) * $limit;
                    $unreadOnly = $_GET['unread'] === 'true';
                    
                    $whereClauses = ['user_id = ?'];
                    $params = [$user['id']];
                    
                    if ($unreadOnly) {
                        $whereClauses[] = 'is_read = 0';
                    }
                    
                    $whereClause = implode(' AND ', $whereClauses);
                    
                    $notifications = db_fetch_all("
                        SELECT * FROM notifications 
                        WHERE $whereClause
                        ORDER BY created_at DESC
                        LIMIT $limit OFFSET $offset
                    ", $params);
                    
                    $total = db_fetch_column("SELECT COUNT(*) FROM notifications WHERE $whereClause", $params);
                    $unreadCount = db_fetch_column("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0", [$user['id']]);
                    
                    sendResponse(200, [
                        'success' => true,
                        'data' => $notifications,
                        'unread_count' => $unreadCount,
                        'pagination' => [
                            'current_page' => $page,
                            'per_page' => $limit,
                            'total' => $total,
                            'total_pages' => ceil($total / $limit)
                        ]
                    ]);
                    break;
                    
                default:
                    sendResponse(405, ['error' => 'Method not allowed']);
            }
            break;
            
        case 'mark-read':
            if ($method !== 'POST') {
                sendResponse(405, ['error' => 'Method not allowed']);
            }
            
            $notificationId = (int)($input['notification_id'] ?? 0);
            
            if ($notificationId) {
                // Mark specific notification as read
                db_query("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?", [$notificationId, $user['id']]);
            } else {
                // Mark all as read
                db_query("UPDATE notifications SET is_read = 1 WHERE user_id = ?", [$user['id']]);
            }
            
            sendResponse(200, [
                'success' => true,
                'message' => 'Notifications marked as read'
            ]);
            break;
            
        default:
            sendResponse(404, ['error' => 'Notifications endpoint not found']);
    }
}

// Ratings handlers
function handleRatings($segments, $method, $input, $user) {
    if ($method !== 'POST') {
        sendResponse(405, ['error' => 'Method not allowed']);
    }
    
    $itemType = $input['item_type'] ?? '';
    $itemId = (int)($input['item_id'] ?? 0);
    $rating = (int)($input['rating'] ?? 0);
    
    if (!$itemType || !$itemId) {
        sendResponse(400, ['error' => 'Item type and ID required']);
    }
    
    if (!in_array($itemType, ['news', 'post', 'event'])) {
        sendResponse(400, ['error' => 'Invalid item type']);
    }
    
    if ($rating < 1 || $rating > 5) {
        sendResponse(400, ['error' => 'Rating must be between 1 and 5']);
    }
    
    // Insert or update rating
    $result = db_query("
        INSERT OR REPLACE INTO ratings (user_id, item_type, item_id, rating) 
        VALUES (?, ?, ?, ?)
    ", [$user['id'], $itemType, $itemId, $rating]);
    
    if ($result) {
        // Get updated rating stats
        $stats = db_fetch_one("
            SELECT COUNT(*) as count, AVG(rating) as average
            FROM ratings 
            WHERE item_type = ? AND item_id = ?
        ", [$itemType, $itemId]);
        
        sendResponse(200, [
            'success' => true,
            'message' => 'Rating submitted',
            'stats' => [
                'rating_count' => (int)$stats['count'],
                'average_rating' => round($stats['average'], 1)
            ]
        ]);
    } else {
        sendResponse(500, ['error' => 'Failed to submit rating']);
    }
}

// Comments handlers
function handleComments($segments, $method, $input, $user) {
    $endpoint = $segments[1] ?? '';
    
    switch ($endpoint) {
        case '':
            switch ($method) {
                case 'GET':
                    // Get comments for item
                    $itemType = $_GET['item_type'] ?? '';
                    $itemId = (int)($_GET['item_id'] ?? 0);
                    
                    if (!$itemType || !$itemId) {
                        sendResponse(400, ['error' => 'Item type and ID required']);
                    }
                    
                    $comments = db_fetch_all("
                        SELECT c.*, u.name as author_name, u.avatar as author_avatar
                        FROM comments c
                        JOIN users u ON c.user_id = u.id
                        WHERE c.item_type = ? AND c.item_id = ? AND c.is_approved = 1
                        ORDER BY c.created_at DESC
                    ", [$itemType, $itemId]);
                    
                    sendResponse(200, [
                        'success' => true,
                        'data' => $comments
                    ]);
                    break;
                    
                case 'POST':
                    // Add comment
                    $itemType = $input['item_type'] ?? '';
                    $itemId = (int)($input['item_id'] ?? 0);
                    $content = $input['content'] ?? '';
                    
                    if (!$itemType || !$itemId || !$content) {
                        sendResponse(400, ['error' => 'Item type, ID and content required']);
                    }
                    
                    if (strlen($content) < 3) {
                        sendResponse(400, ['error' => 'Comment too short']);
                    }
                    
                    $commentId = db_insert_id("
                        INSERT INTO comments (user_id, item_type, item_id, content, is_approved) 
                        VALUES (?, ?, ?, ?, 1)
                    ", [$user['id'], $itemType, $itemId, $content]);
                    
                    if ($commentId) {
                        sendResponse(201, [
                            'success' => true,
                            'message' => 'Comment added',
                            'comment_id' => $commentId
                        ]);
                    } else {
                        sendResponse(500, ['error' => 'Failed to add comment']);
                    }
                    break;
                    
                default:
                    sendResponse(405, ['error' => 'Method not allowed']);
            }
            break;
            
        default:
            // Handle specific comment operations
            if (!is_numeric($endpoint)) {
                sendResponse(404, ['error' => 'Comment not found']);
            }
            
            $commentId = (int)$endpoint;
            
            switch ($method) {
                case 'PUT':
                    // Update comment
                    $content = $input['content'] ?? '';
                    
                    if (!$content) {
                        sendResponse(400, ['error' => 'Content required']);
                    }
                    
                    // Check ownership
                    $comment = db_fetch_one("SELECT * FROM comments WHERE id = ? AND user_id = ?", [$commentId, $user['id']]);
                    if (!$comment) {
                        sendResponse(404, ['error' => 'Comment not found or access denied']);
                    }
                    
                    $result = db_query("UPDATE comments SET content = ? WHERE id = ?", [$content, $commentId]);
                    
                    sendResponse(200, [
                        'success' => true,
                        'message' => 'Comment updated'
                    ]);
                    break;
                    
                case 'DELETE':
                    // Delete comment
                    $comment = db_fetch_one("SELECT * FROM comments WHERE id = ? AND user_id = ?", [$commentId, $user['id']]);
                    if (!$comment && $user['role'] !== 'admin') {
                        sendResponse(404, ['error' => 'Comment not found or access denied']);
                    }
                    
                    $result = db_query("DELETE FROM comments WHERE id = ?", [$commentId]);
                    
                    sendResponse(200, [
                        'success' => true,
                        'message' => 'Comment deleted'
                    ]);
                    break;
                    
                default:
                    sendResponse(405, ['error' => 'Method not allowed']);
            }
    }
}

// Recommendations handlers
function handleRecommendations($segments, $method, $input, $user) {
    if ($method !== 'GET') {
        sendResponse(405, ['error' => 'Method not allowed']);
    }
    
    $limit = min((int)($_GET['limit'] ?? 10), 50);
    
    require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/recommendations.php';
    
    $recommendations = RecommendationEngine::getRecommendations($user['id'], $limit);
    
    sendResponse(200, [
        'success' => true,
        'data' => $recommendations
    ]);
}
?>