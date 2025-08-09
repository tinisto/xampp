<?php
/**
 * API endpoint for threaded comments loading
 * Supports parent-child relationship with nested replies
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Check if it's a GET request
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Get parameters
$entityType = $_GET['entity_type'] ?? '';
$entityId = (int)($_GET['entity_id'] ?? 0);
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = min(50, max(5, (int)($_GET['limit'] ?? 10))); // Limit between 5-50

// Validate parameters
if (empty($entityType) || $entityId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid parameters']);
    exit;
}

try {
    // Calculate offset
    $offset = ($page - 1) * $limit;
    
    // First, get total counts for statistics
    $totalCommentsQuery = "SELECT COUNT(*) as total FROM comments WHERE entity_type = ? AND entity_id = ? AND (parent_id IS NULL OR parent_id = 0)";
    $stmt = $connection->prepare($totalCommentsQuery);
    $stmt->bind_param("si", $entityType, $entityId);
    $stmt->execute();
    $totalComments = $stmt->get_result()->fetch_assoc()['total'];
    
    $totalRepliesQuery = "SELECT COUNT(*) as total FROM comments WHERE entity_type = ? AND entity_id = ? AND parent_id IS NOT NULL AND parent_id > 0";
    $stmt = $connection->prepare($totalRepliesQuery);
    $stmt->bind_param("si", $entityType, $entityId);
    $stmt->execute();
    $totalReplies = $stmt->get_result()->fetch_assoc()['total'];
    
    // Calculate total pages based on parent comments only
    $totalPages = ceil($totalComments / $limit);
    
    // Get parent comments for this page with likes/dislikes and edit info
    $parentQuery = "SELECT id, user_id, author_of_comment, comment_text, date, edited_at, edit_count, entity_type, entity_id, parent_id, email, likes, dislikes
                    FROM comments 
                    WHERE entity_type = ? AND entity_id = ? AND (parent_id IS NULL OR parent_id = 0)
                    ORDER BY date DESC 
                    LIMIT ? OFFSET ?";
    
    $stmt = $connection->prepare($parentQuery);
    $stmt->bind_param("siii", $entityType, $entityId, $limit, $offset);
    $stmt->execute();
    $parentResult = $stmt->get_result();
    
    $parentComments = [];
    $parentIds = [];
    
    while ($row = $parentResult->fetch_assoc()) {
        $parentComments[] = $row;
        $parentIds[] = $row['id'];
    }
    
    // Get all replies for these parent comments
    $allComments = $parentComments;
    
    if (!empty($parentIds)) {
        $parentIdsStr = implode(',', array_map('intval', $parentIds));
        
        // Get all nested replies using recursive approach with likes/dislikes and edit info
        $repliesQuery = "WITH RECURSIVE comment_tree AS (
                            SELECT id, user_id, author_of_comment, comment_text, date, edited_at, edit_count, entity_type, entity_id, parent_id, email, likes, dislikes, 1 as depth
                            FROM comments 
                            WHERE entity_type = ? AND entity_id = ? AND parent_id IN ($parentIdsStr)
                            
                            UNION ALL
                            
                            SELECT c.id, c.user_id, c.author_of_comment, c.comment_text, c.date, c.edited_at, c.edit_count, c.entity_type, c.entity_id, c.parent_id, c.email, c.likes, c.dislikes, ct.depth + 1
                            FROM comments c
                            INNER JOIN comment_tree ct ON c.parent_id = ct.id
                            WHERE c.entity_type = ? AND c.entity_id = ? AND ct.depth < 10
                        )
                        SELECT * FROM comment_tree ORDER BY parent_id, date ASC";
        
        $stmt = $connection->prepare($repliesQuery);
        $stmt->bind_param("sisi", $entityType, $entityId, $entityType, $entityId);
        $stmt->execute();
        $repliesResult = $stmt->get_result();
        
        while ($row = $repliesResult->fetch_assoc()) {
            $allComments[] = $row;
        }
    }
    
    // Sort comments properly for display
    usort($allComments, function($a, $b) {
        // Parent comments first, sorted by date desc
        if ((!$a['parent_id'] || $a['parent_id'] == 0) && (!$b['parent_id'] || $b['parent_id'] == 0)) {
            return strtotime($b['date']) - strtotime($a['date']);
        }
        
        // If one is parent and other is child, parent comes first
        if (!$a['parent_id'] || $a['parent_id'] == 0) return -1;
        if (!$b['parent_id'] || $b['parent_id'] == 0) return 1;
        
        // Both are replies, sort by date asc
        return strtotime($a['date']) - strtotime($b['date']);
    });
    
    // Clean up comment data for frontend
    $cleanComments = [];
    foreach ($allComments as $comment) {
        $cleanComments[] = [
            'id' => (int)$comment['id'],
            'author_of_comment' => $comment['author_of_comment'] ?: 'Аноним',
            'comment_text' => $comment['comment_text'],
            'date' => $comment['date'],
            'entity_type' => $comment['entity_type'],
            'entity_id' => (int)$comment['entity_id'],
            'parent_id' => $comment['parent_id'] ? (int)$comment['parent_id'] : null,
            'user_id' => $comment['user_id'] ? (int)$comment['user_id'] : null,
            'likes' => (int)($comment['likes'] ?? 0),
            'dislikes' => (int)($comment['dislikes'] ?? 0),
            'edited_at' => $comment['edited_at'],
            'edit_count' => (int)($comment['edit_count'] ?? 0)
        ];
    }
    
    // Return successful response
    echo json_encode([
        'success' => true,
        'comments' => $cleanComments,
        'currentPage' => $page,
        'totalPages' => $totalPages,
        'totalComments' => $totalComments,
        'totalReplies' => $totalReplies,
        'hasMore' => $page < $totalPages
    ]);
    
} catch (Exception $e) {
    error_log("Threaded comments API error: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error occurred',
        'debug' => $e->getMessage() // Remove in production
    ]);
}
?>