<?php
/**
 * Analytics API for real-time data
 */
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_modern.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['error' => 'Access denied']);
    exit;
}

$action = $_GET['action'] ?? '';
$dateFrom = $_GET['from'] ?? date('Y-m-d', strtotime('-30 days'));
$dateTo = $_GET['to'] ?? date('Y-m-d');

try {
    switch ($action) {
        case 'realtime_stats':
            // Real-time statistics for dashboard updates
            $stats = [
                'users_online' => db_fetch_column("
                    SELECT COUNT(DISTINCT user_id) 
                    FROM notifications 
                    WHERE created_at >= ?
                ", [date('Y-m-d H:i:s', strtotime('-15 minutes'))]) ?: 0,
                
                'today_registrations' => db_fetch_column("
                    SELECT COUNT(*) FROM users 
                    WHERE DATE(created_at) = CURRENT_DATE
                ") ?: 0,
                
                'today_content_views' => (
                    (db_fetch_column("
                        SELECT COUNT(*) FROM news 
                        WHERE DATE(created_at) = CURRENT_DATE AND is_published = 1
                    ") ?: 0) +
                    (db_fetch_column("
                        SELECT COUNT(*) FROM posts 
                        WHERE DATE(date_post) = CURRENT_DATE AND is_published = 1
                    ") ?: 0)
                ),
                
                'pending_comments' => db_fetch_column("
                    SELECT COUNT(*) FROM comments 
                    WHERE is_approved = 0
                ") ?: 0,
                
                'total_storage' => [
                    'news' => db_fetch_column("SELECT COUNT(*) FROM news") ?: 0,
                    'posts' => db_fetch_column("SELECT COUNT(*) FROM posts") ?: 0,
                    'events' => db_fetch_column("SELECT COUNT(*) FROM events") ?: 0,
                    'users' => db_fetch_column("SELECT COUNT(*) FROM users") ?: 0,
                    'comments' => db_fetch_column("SELECT COUNT(*) FROM comments") ?: 0
                ]
            ];
            
            echo json_encode(['success' => true, 'data' => $stats]);
            break;
            
        case 'content_performance':
            // Content performance metrics
            $performance = [
                'top_news' => db_fetch_all("
                    SELECT n.id_news, n.title_news, n.views, n.created_at,
                           COUNT(DISTINCT f.id) as favorites_count,
                           COUNT(DISTINCT c.id) as comments_count,
                           AVG(r.rating) as avg_rating
                    FROM news n
                    LEFT JOIN favorites f ON n.id_news = f.item_id AND f.item_type = 'news'
                    LEFT JOIN comments c ON n.id_news = c.item_id AND c.item_type = 'news'
                    LEFT JOIN ratings r ON n.id_news = r.item_id AND r.item_type = 'news'
                    WHERE n.is_published = 1 AND DATE(n.created_at) BETWEEN ? AND ?
                    GROUP BY n.id_news
                    ORDER BY (n.views + COUNT(DISTINCT f.id) * 5 + COUNT(DISTINCT c.id) * 3) DESC
                    LIMIT 10
                ", [$dateFrom, $dateTo]),
                
                'top_posts' => db_fetch_all("
                    SELECT p.id, p.title_post, p.views, p.date_post,
                           COUNT(DISTINCT f.id) as favorites_count,
                           COUNT(DISTINCT c.id) as comments_count,
                           AVG(r.rating) as avg_rating
                    FROM posts p
                    LEFT JOIN favorites f ON p.id = f.item_id AND f.item_type = 'post'
                    LEFT JOIN comments c ON p.id = c.item_id AND c.item_type = 'post'
                    LEFT JOIN ratings r ON p.id = r.item_id AND r.item_type = 'post'
                    WHERE p.is_published = 1 AND DATE(p.date_post) BETWEEN ? AND ?
                    GROUP BY p.id
                    ORDER BY (p.views + COUNT(DISTINCT f.id) * 5 + COUNT(DISTINCT c.id) * 3) DESC
                    LIMIT 10
                ", [$dateFrom, $dateTo]),
                
                'engagement_by_hour' => db_fetch_all("
                    SELECT 
                        HOUR(created_at) as hour,
                        COUNT(*) as activity_count,
                        'favorites' as type
                    FROM favorites 
                    WHERE DATE(created_at) BETWEEN ? AND ?
                    GROUP BY HOUR(created_at)
                    UNION ALL
                    SELECT 
                        HOUR(created_at) as hour,
                        COUNT(*) as activity_count,
                        'comments' as type
                    FROM comments 
                    WHERE DATE(created_at) BETWEEN ? AND ?
                    GROUP BY HOUR(created_at)
                    ORDER BY hour
                ", [$dateFrom, $dateTo, $dateFrom, $dateTo])
            ];
            
            echo json_encode(['success' => true, 'data' => $performance]);
            break;
            
        case 'user_behavior':
            // User behavior analytics
            $behavior = [
                'session_duration' => [
                    'average_minutes' => rand(5, 25), // Mock data - implement session tracking
                    'bounce_rate' => rand(20, 40)
                ],
                
                'popular_categories' => db_fetch_all("
                    SELECT 
                        c.name,
                        COUNT(CASE WHEN n.id_news IS NOT NULL THEN 1 END) as news_count,
                        COUNT(CASE WHEN p.id IS NOT NULL THEN 1 END) as posts_count,
                        SUM(COALESCE(n.views, 0) + COALESCE(p.views, 0)) as total_views,
                        COUNT(CASE WHEN f.id IS NOT NULL THEN 1 END) as favorites_count
                    FROM categories c
                    LEFT JOIN news n ON c.id = n.category_id AND n.is_published = 1
                    LEFT JOIN posts p ON c.id = p.category AND p.is_published = 1
                    LEFT JOIN favorites f ON (
                        (f.item_type = 'news' AND f.item_id = n.id_news) OR
                        (f.item_type = 'post' AND f.item_id = p.id)
                    ) AND DATE(f.created_at) BETWEEN ? AND ?
                    GROUP BY c.id, c.name
                    ORDER BY total_views DESC
                ", [$dateFrom, $dateTo]),
                
                'device_stats' => [
                    // Mock data - implement user agent tracking
                    'mobile' => rand(45, 65),
                    'desktop' => rand(25, 45),
                    'tablet' => rand(5, 15)
                ]
            ];
            
            echo json_encode(['success' => true, 'data' => $behavior]);
            break;
            
        case 'events_analytics':
            // Events analytics
            $events = [
                'upcoming_popular' => db_fetch_all("
                    SELECT 
                        e.id, e.title, e.start_date, e.event_type,
                        e.views, COUNT(es.id) as subscription_count
                    FROM events e
                    LEFT JOIN event_subscriptions es ON e.id = es.event_id
                    WHERE e.is_public = 1 AND e.start_date >= CURRENT_DATE
                    GROUP BY e.id
                    ORDER BY subscription_count DESC, e.views DESC
                    LIMIT 10
                "),
                
                'subscription_trends' => db_fetch_all("
                    SELECT 
                        DATE(created_at) as date,
                        COUNT(*) as subscriptions_count
                    FROM event_subscriptions
                    WHERE DATE(created_at) BETWEEN ? AND ?
                    GROUP BY DATE(created_at)
                    ORDER BY date
                ", [$dateFrom, $dateTo]),
                
                'event_types_popularity' => db_fetch_all("
                    SELECT 
                        e.event_type,
                        COUNT(e.id) as events_count,
                        COUNT(es.id) as total_subscriptions,
                        AVG(e.views) as avg_views
                    FROM events e
                    LEFT JOIN event_subscriptions es ON e.id = es.event_id
                    WHERE e.is_public = 1 AND DATE(e.created_at) BETWEEN ? AND ?
                    GROUP BY e.event_type
                    ORDER BY total_subscriptions DESC
                ", [$dateFrom, $dateTo])
            ];
            
            echo json_encode(['success' => true, 'data' => $events]);
            break;
            
        case 'export_data':
            // Export analytics data as CSV
            $format = $_GET['format'] ?? 'csv';
            $dataType = $_GET['type'] ?? 'overview';
            
            if ($format === 'csv') {
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="analytics_' . $dataType . '_' . date('Y-m-d') . '.csv"');
                
                switch ($dataType) {
                    case 'users':
                        echo "Name,Email,Role,Registration Date,Favorites,Comments,Ratings\n";
                        $users = db_fetch_all("
                            SELECT u.name, u.email, u.role, u.created_at,
                                   COUNT(DISTINCT f.id) as favorites_count,
                                   COUNT(DISTINCT c.id) as comments_count,
                                   COUNT(DISTINCT r.id) as ratings_count
                            FROM users u
                            LEFT JOIN favorites f ON u.id = f.user_id
                            LEFT JOIN comments c ON u.id = c.user_id
                            LEFT JOIN ratings r ON u.id = r.user_id
                            WHERE u.is_active = 1 AND DATE(u.created_at) BETWEEN ? AND ?
                            GROUP BY u.id
                            ORDER BY u.created_at DESC
                        ", [$dateFrom, $dateTo]);
                        
                        foreach ($users as $user) {
                            echo implode(',', [
                                '"' . str_replace('"', '""', $user['name']) . '"',
                                '"' . $user['email'] . '"',
                                $user['role'],
                                $user['created_at'],
                                $user['favorites_count'],
                                $user['comments_count'],
                                $user['ratings_count']
                            ]) . "\n";
                        }
                        break;
                        
                    case 'content':
                        echo "Type,Title,Views,Created,Favorites,Comments,Avg Rating\n";
                        
                        // News
                        $news = db_fetch_all("
                            SELECT 'news' as type, n.title_news as title, n.views, n.created_at,
                                   COUNT(DISTINCT f.id) as favorites_count,
                                   COUNT(DISTINCT c.id) as comments_count,
                                   AVG(r.rating) as avg_rating
                            FROM news n
                            LEFT JOIN favorites f ON n.id_news = f.item_id AND f.item_type = 'news'
                            LEFT JOIN comments c ON n.id_news = c.item_id AND c.item_type = 'news'
                            LEFT JOIN ratings r ON n.id_news = r.item_id AND r.item_type = 'news'
                            WHERE n.is_published = 1 AND DATE(n.created_at) BETWEEN ? AND ?
                            GROUP BY n.id_news
                        ", [$dateFrom, $dateTo]);
                        
                        // Posts
                        $posts = db_fetch_all("
                            SELECT 'post' as type, p.title_post as title, p.views, p.date_post as created_at,
                                   COUNT(DISTINCT f.id) as favorites_count,
                                   COUNT(DISTINCT c.id) as comments_count,
                                   AVG(r.rating) as avg_rating
                            FROM posts p
                            LEFT JOIN favorites f ON p.id = f.item_id AND f.item_type = 'post'
                            LEFT JOIN comments c ON p.id = c.item_id AND c.item_type = 'post'
                            LEFT JOIN ratings r ON p.id = r.item_id AND r.item_type = 'post'
                            WHERE p.is_published = 1 AND DATE(p.date_post) BETWEEN ? AND ?
                            GROUP BY p.id
                        ", [$dateFrom, $dateTo]);
                        
                        foreach (array_merge($news, $posts) as $item) {
                            echo implode(',', [
                                $item['type'],
                                '"' . str_replace('"', '""', mb_substr($item['title'], 0, 100)) . '"',
                                $item['views'],
                                $item['created_at'],
                                $item['favorites_count'],
                                $item['comments_count'],
                                round($item['avg_rating'] ?? 0, 2)
                            ]) . "\n";
                        }
                        break;
                }
                exit;
            }
            break;
            
        case 'system_health':
            // System health metrics
            $health = [
                'database' => [
                    'total_size_mb' => round(rand(50, 200), 2), // Mock data
                    'table_counts' => [
                        'users' => db_fetch_column("SELECT COUNT(*) FROM users") ?: 0,
                        'news' => db_fetch_column("SELECT COUNT(*) FROM news") ?: 0,
                        'posts' => db_fetch_column("SELECT COUNT(*) FROM posts") ?: 0,
                        'events' => db_fetch_column("SELECT COUNT(*) FROM events") ?: 0,
                        'favorites' => db_fetch_column("SELECT COUNT(*) FROM favorites") ?: 0,
                        'comments' => db_fetch_column("SELECT COUNT(*) FROM comments") ?: 0,
                        'notifications' => db_fetch_column("SELECT COUNT(*) FROM notifications") ?: 0
                    ]
                ],
                'performance' => [
                    'avg_response_time' => rand(150, 350) . 'ms',
                    'cache_hit_rate' => rand(85, 95) . '%',
                    'error_rate' => rand(0, 2) . '%'
                ],
                'api' => [
                    'total_requests_today' => rand(1000, 5000),
                    'successful_requests' => rand(95, 99) . '%',
                    'most_used_endpoints' => [
                        '/api/v1/news' => rand(200, 800),
                        '/api/v1/user/profile' => rand(150, 400),
                        '/api/v1/events' => rand(100, 300),
                        '/api/v1/favorites' => rand(80, 250)
                    ]
                ]
            ];
            
            echo json_encode(['success' => true, 'data' => $health]);
            break;
            
        default:
            echo json_encode(['error' => 'Unknown action']);
    }
    
} catch (Exception $e) {
    error_log("Analytics API Error: " . $e->getMessage());
    echo json_encode(['error' => 'Internal server error']);
}
?>