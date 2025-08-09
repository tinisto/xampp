<?php
/**
 * Comment Analytics API
 * Provides real-time analytics data for comments
 */

session_start();
header('Content-Type: application/json');

// Check admin access
if ((!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') && 
    (!isset($_SESSION['occupation']) || $_SESSION['occupation'] !== 'admin')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Access denied']);
    exit();
}

// Database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Get parameters
$type = $_GET['type'] ?? 'summary';
$entity_type = $_GET['entity_type'] ?? null;
$entity_id = (int)($_GET['entity_id'] ?? 0);
$period = $_GET['period'] ?? '30d'; // 7d, 30d, 90d, 1y, all

// Calculate date range
$end_date = date('Y-m-d H:i:s');
switch ($period) {
    case '7d':
        $start_date = date('Y-m-d H:i:s', strtotime('-7 days'));
        break;
    case '30d':
        $start_date = date('Y-m-d H:i:s', strtotime('-30 days'));
        break;
    case '90d':
        $start_date = date('Y-m-d H:i:s', strtotime('-90 days'));
        break;
    case '1y':
        $start_date = date('Y-m-d H:i:s', strtotime('-1 year'));
        break;
    case 'all':
        $start_date = '2000-01-01 00:00:00';
        break;
    default:
        $start_date = date('Y-m-d H:i:s', strtotime('-30 days'));
}

try {
    $response = [];
    
    switch ($type) {
        case 'summary':
            // Overall summary statistics
            $query = "SELECT 
                COUNT(*) as total_comments,
                COUNT(DISTINCT CASE WHEN user_id IS NOT NULL THEN user_id ELSE author_ip END) as unique_commenters,
                COUNT(CASE WHEN parent_id IS NOT NULL THEN 1 END) as total_replies,
                AVG(CHAR_LENGTH(comment_text)) as avg_length,
                SUM(likes) as total_likes,
                SUM(dislikes) as total_dislikes,
                COUNT(CASE WHEN edited_at IS NOT NULL THEN 1 END) as edited_count,
                AVG(CASE WHEN parent_id IS NULL THEN 
                    (SELECT COUNT(*) FROM comments c2 WHERE c2.parent_id = comments.id)
                ELSE 0 END) as avg_replies_per_comment
                FROM comments
                WHERE date BETWEEN ? AND ?";
            
            $params = [$start_date, $end_date];
            
            if ($entity_type && $entity_id) {
                $query .= " AND entity_type = ? AND entity_id = ?";
                $params[] = $entity_type;
                $params[] = $entity_id;
            }
            
            $stmt = $connection->prepare($query);
            $stmt->bind_param(str_repeat('s', count($params)), ...$params);
            $stmt->execute();
            $response = $stmt->get_result()->fetch_assoc();
            
            // Calculate engagement rate
            $response['engagement_rate'] = $response['total_comments'] > 0 
                ? round(($response['total_likes'] + $response['total_dislikes']) / $response['total_comments'] * 100, 2)
                : 0;
            
            // Reply rate
            $response['reply_rate'] = $response['total_comments'] > 0
                ? round($response['total_replies'] / $response['total_comments'] * 100, 2)
                : 0;
            
            break;
            
        case 'timeline':
            // Comments over time
            $interval = $period === '7d' ? 'DAY' : ($period === '30d' ? 'DAY' : 'WEEK');
            
            $query = "SELECT 
                DATE_FORMAT(date, '%Y-%m-%d') as period,
                COUNT(*) as comments,
                COUNT(DISTINCT CASE WHEN user_id IS NOT NULL THEN user_id ELSE author_ip END) as unique_users,
                SUM(likes) as likes,
                SUM(dislikes) as dislikes
                FROM comments
                WHERE date BETWEEN ? AND ?";
            
            $params = [$start_date, $end_date];
            
            if ($entity_type && $entity_id) {
                $query .= " AND entity_type = ? AND entity_id = ?";
                $params[] = $entity_type;
                $params[] = $entity_id;
            }
            
            $query .= " GROUP BY period ORDER BY period";
            
            $stmt = $connection->prepare($query);
            $stmt->bind_param(str_repeat('s', count($params)), ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $response = [];
            while ($row = $result->fetch_assoc()) {
                $response[] = $row;
            }
            break;
            
        case 'sentiment':
            // Sentiment analysis based on likes/dislikes ratio
            $query = "SELECT 
                COUNT(CASE WHEN likes > dislikes THEN 1 END) as positive,
                COUNT(CASE WHEN likes < dislikes THEN 1 END) as negative,
                COUNT(CASE WHEN likes = dislikes THEN 1 END) as neutral,
                AVG(CASE WHEN likes + dislikes > 0 THEN likes / (likes + dislikes) ELSE 0.5 END) as positivity_score
                FROM comments
                WHERE date BETWEEN ? AND ?";
            
            $params = [$start_date, $end_date];
            
            if ($entity_type && $entity_id) {
                $query .= " AND entity_type = ? AND entity_id = ?";
                $params[] = $entity_type;
                $params[] = $entity_id;
            }
            
            $stmt = $connection->prepare($query);
            $stmt->bind_param(str_repeat('s', count($params)), ...$params);
            $stmt->execute();
            $response = $stmt->get_result()->fetch_assoc();
            
            $response['positivity_score'] = round($response['positivity_score'] * 100, 2);
            break;
            
        case 'top_threads':
            // Most engaging comment threads
            $query = "SELECT 
                c.id, c.comment_text, c.author_of_comment, c.date,
                c.likes, c.dislikes, c.entity_type, c.entity_id,
                COUNT(r.id) as reply_count,
                (c.likes + c.dislikes + COUNT(r.id) * 2) as engagement_score
                FROM comments c
                LEFT JOIN comments r ON r.parent_id = c.id
                WHERE c.parent_id IS NULL AND c.date BETWEEN ? AND ?";
            
            $params = [$start_date, $end_date];
            
            if ($entity_type && $entity_id) {
                $query .= " AND c.entity_type = ? AND c.entity_id = ?";
                $params[] = $entity_type;
                $params[] = $entity_id;
            }
            
            $query .= " GROUP BY c.id ORDER BY engagement_score DESC LIMIT 10";
            
            $stmt = $connection->prepare($query);
            $stmt->bind_param(str_repeat('s', count($params)), ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $response = [];
            while ($row = $result->fetch_assoc()) {
                $response[] = $row;
            }
            break;
            
        case 'user_activity':
            // User activity patterns
            $query = "SELECT 
                HOUR(date) as hour,
                DAYOFWEEK(date) as day_of_week,
                COUNT(*) as comment_count
                FROM comments
                WHERE date BETWEEN ? AND ?";
            
            $params = [$start_date, $end_date];
            
            if ($entity_type && $entity_id) {
                $query .= " AND entity_type = ? AND entity_id = ?";
                $params[] = $entity_type;
                $params[] = $entity_id;
            }
            
            $query .= " GROUP BY hour, day_of_week";
            
            $stmt = $connection->prepare($query);
            $stmt->bind_param(str_repeat('s', count($params)), ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
            
            // Build heatmap data
            $heatmap = array_fill(0, 7, array_fill(0, 24, 0));
            while ($row = $result->fetch_assoc()) {
                $heatmap[$row['day_of_week'] - 1][$row['hour']] = (int)$row['comment_count'];
            }
            
            $response = [
                'heatmap' => $heatmap,
                'days' => ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб']
            ];
            break;
            
        case 'word_cloud':
            // Most common words in comments
            $query = "SELECT comment_text FROM comments WHERE date BETWEEN ? AND ?";
            
            $params = [$start_date, $end_date];
            
            if ($entity_type && $entity_id) {
                $query .= " AND entity_type = ? AND entity_id = ?";
                $params[] = $entity_type;
                $params[] = $entity_id;
            }
            
            $query .= " LIMIT 1000"; // Limit for performance
            
            $stmt = $connection->prepare($query);
            $stmt->bind_param(str_repeat('s', count($params)), ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
            
            // Process words
            $word_counts = [];
            $stop_words = ['и', 'в', 'на', 'с', 'по', 'для', 'не', 'что', 'это', 'как', 'но', 'или', 'а', 'у'];
            
            while ($row = $result->fetch_assoc()) {
                $words = preg_split('/\s+/', mb_strtolower($row['comment_text'], 'UTF-8'));
                foreach ($words as $word) {
                    $word = trim($word, '.,!?;:"');
                    if (mb_strlen($word) > 3 && !in_array($word, $stop_words)) {
                        $word_counts[$word] = ($word_counts[$word] ?? 0) + 1;
                    }
                }
            }
            
            arsort($word_counts);
            $response = array_slice($word_counts, 0, 50, true);
            break;
            
        default:
            throw new Exception('Invalid analytics type');
    }
    
    echo json_encode([
        'success' => true,
        'type' => $type,
        'period' => $period,
        'data' => $response
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Analytics error: ' . $e->getMessage()
    ]);
}
?>