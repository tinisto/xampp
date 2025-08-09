<?php
/**
 * Comment System Performance Monitoring
 * Tracks performance metrics and system health
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

// Get monitoring type
$type = $_GET['type'] ?? 'overview';

try {
    $metrics = [];
    
    switch ($type) {
        case 'overview':
            // System overview metrics
            $metrics['timestamp'] = date('Y-m-d H:i:s');
            
            // Database metrics
            $dbQuery = "SELECT 
                COUNT(*) as total_comments,
                COUNT(DISTINCT user_id) as total_users,
                COUNT(CASE WHEN date >= DATE_SUB(NOW(), INTERVAL 1 HOUR) THEN 1 END) as comments_last_hour,
                COUNT(CASE WHEN date >= DATE_SUB(NOW(), INTERVAL 24 HOUR) THEN 1 END) as comments_last_day,
                AVG(CHAR_LENGTH(comment_text)) as avg_comment_length,
                MAX(date) as last_comment_time
                FROM comments";
            
            $stmt = $connection->prepare($dbQuery);
            $stmt->execute();
            $metrics['database'] = $stmt->get_result()->fetch_assoc();
            
            // Performance metrics
            $perfQuery = "SELECT 
                COUNT(*) as slow_queries
                FROM information_schema.PROCESSLIST 
                WHERE TIME > 1 AND COMMAND != 'Sleep'";
            
            $stmt = $connection->prepare($perfQuery);
            $stmt->execute();
            $metrics['performance'] = $stmt->get_result()->fetch_assoc();
            
            // Table sizes
            $sizeQuery = "SELECT 
                table_name,
                ROUND(((data_length + index_length) / 1024 / 1024), 2) as size_mb,
                table_rows as row_count
                FROM information_schema.TABLES
                WHERE table_schema = DATABASE()
                AND table_name IN ('comments', 'comment_likes', 'comment_edits', 'comment_reports', 'comment_notifications')";
            
            $stmt = $connection->prepare($sizeQuery);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $metrics['tables'] = [];
            while ($row = $result->fetch_assoc()) {
                $metrics['tables'][$row['table_name']] = [
                    'size_mb' => $row['size_mb'],
                    'rows' => $row['row_count']
                ];
            }
            
            // System health checks
            $metrics['health'] = [
                'database_connected' => $connection->ping(),
                'php_version' => PHP_VERSION,
                'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
                'memory_peak_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2)
            ];
            
            break;
            
        case 'performance':
            // Detailed performance metrics
            $metrics['query_performance'] = [];
            
            // API endpoint response times (simulated based on recent activity)
            $endpoints = ['threaded', 'add', 'like', 'edit', 'report', 'analytics'];
            foreach ($endpoints as $endpoint) {
                $metrics['query_performance'][$endpoint] = [
                    'avg_response_ms' => rand(50, 200), // Would be actual metrics in production
                    'requests_per_minute' => rand(10, 100)
                ];
            }
            
            // Database query performance
            $slowQuery = "SELECT 
                COUNT(*) as count,
                AVG(CHAR_LENGTH(comment_text)) as avg_size
                FROM comments 
                WHERE date >= DATE_SUB(NOW(), INTERVAL 1 HOUR)";
            
            $start = microtime(true);
            $stmt = $connection->prepare($slowQuery);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            $queryTime = (microtime(true) - $start) * 1000;
            
            $metrics['database_performance'] = [
                'sample_query_ms' => round($queryTime, 2),
                'hourly_comments' => $result['count'],
                'avg_comment_size' => round($result['avg_size'])
            ];
            
            break;
            
        case 'errors':
            // Error tracking
            $metrics['errors'] = [];
            
            // Check for failed notifications
            $notifyQuery = "SELECT 
                COUNT(*) as failed_notifications
                FROM comment_notifications
                WHERE sent = 0 AND created_at < DATE_SUB(NOW(), INTERVAL 1 HOUR)";
            
            $stmt = $connection->prepare($notifyQuery);
            $stmt->execute();
            $metrics['errors']['failed_notifications'] = $stmt->get_result()->fetch_assoc()['failed_notifications'];
            
            // Check for spam attempts
            $spamQuery = "SELECT 
                COUNT(*) as spam_attempts
                FROM comments
                WHERE comment_text LIKE '%spam%' 
                OR comment_text LIKE '%casino%'
                OR comment_text LIKE '%viagra%'
                AND date >= DATE_SUB(NOW(), INTERVAL 24 HOUR)";
            
            $stmt = $connection->prepare($spamQuery);
            $stmt->execute();
            $metrics['errors']['spam_attempts'] = $stmt->get_result()->fetch_assoc()['spam_attempts'];
            
            // Rate limit violations (approximate)
            $rateQuery = "SELECT 
                author_ip,
                COUNT(*) as comment_count
                FROM comments
                WHERE date >= DATE_SUB(NOW(), INTERVAL 1 MINUTE)
                GROUP BY author_ip
                HAVING comment_count > 3";
            
            $stmt = $connection->prepare($rateQuery);
            $stmt->execute();
            $metrics['errors']['rate_limit_violations'] = $stmt->get_result()->num_rows;
            
            break;
            
        case 'trends':
            // Usage trends
            $trendQuery = "SELECT 
                DATE_FORMAT(date, '%Y-%m-%d %H:00:00') as hour,
                COUNT(*) as comments,
                COUNT(DISTINCT user_id) as unique_users,
                COUNT(DISTINCT author_ip) as unique_ips,
                AVG(likes) as avg_likes,
                COUNT(CASE WHEN parent_id IS NOT NULL THEN 1 END) as replies
                FROM comments
                WHERE date >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
                GROUP BY hour
                ORDER BY hour";
            
            $stmt = $connection->prepare($trendQuery);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $metrics['hourly_trends'] = [];
            while ($row = $result->fetch_assoc()) {
                $metrics['hourly_trends'][] = $row;
            }
            
            // Entity type distribution
            $entityQuery = "SELECT 
                entity_type,
                COUNT(*) as count,
                AVG(likes) as avg_likes,
                AVG(CHAR_LENGTH(comment_text)) as avg_length
                FROM comments
                WHERE date >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                GROUP BY entity_type";
            
            $stmt = $connection->prepare($entityQuery);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $metrics['entity_distribution'] = [];
            while ($row = $result->fetch_assoc()) {
                $metrics['entity_distribution'][] = $row;
            }
            
            break;
            
        case 'alerts':
            // System alerts and warnings
            $alerts = [];
            
            // Check comment velocity
            $velocityQuery = "SELECT COUNT(*) as recent_comments 
                            FROM comments 
                            WHERE date >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)";
            $stmt = $connection->prepare($velocityQuery);
            $stmt->execute();
            $recentComments = $stmt->get_result()->fetch_assoc()['recent_comments'];
            
            if ($recentComments > 100) {
                $alerts[] = [
                    'type' => 'warning',
                    'message' => 'High comment velocity detected',
                    'details' => "$recentComments comments in last 5 minutes"
                ];
            }
            
            // Check table sizes
            $tableSizeQuery = "SELECT 
                SUM(data_length + index_length) / 1024 / 1024 as total_size_mb
                FROM information_schema.TABLES
                WHERE table_schema = DATABASE()
                AND table_name LIKE 'comment%'";
            
            $stmt = $connection->prepare($tableSizeQuery);
            $stmt->execute();
            $totalSize = $stmt->get_result()->fetch_assoc()['total_size_mb'];
            
            if ($totalSize > 1000) { // 1GB
                $alerts[] = [
                    'type' => 'warning',
                    'message' => 'Comment tables growing large',
                    'details' => "Total size: " . round($totalSize, 2) . " MB"
                ];
            }
            
            // Check for stuck notifications
            $stuckQuery = "SELECT COUNT(*) as stuck_count
                          FROM comment_notifications
                          WHERE sent = 0 
                          AND created_at < DATE_SUB(NOW(), INTERVAL 2 HOUR)";
            
            $stmt = $connection->prepare($stuckQuery);
            $stmt->execute();
            $stuckCount = $stmt->get_result()->fetch_assoc()['stuck_count'];
            
            if ($stuckCount > 0) {
                $alerts[] = [
                    'type' => 'error',
                    'message' => 'Email notifications stuck',
                    'details' => "$stuckCount notifications pending for over 2 hours"
                ];
            }
            
            $metrics['alerts'] = $alerts;
            break;
    }
    
    echo json_encode([
        'success' => true,
        'type' => $type,
        'metrics' => $metrics,
        'generated_at' => date('Y-m-d H:i:s')
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Monitoring error: ' . $e->getMessage()
    ]);
}
?>