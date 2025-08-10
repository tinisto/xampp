<?php
/**
 * Content API handlers (news, posts, events, schools, etc.)
 */

// News handlers
function handleNews($segments, $method, $input, $user) {
    $endpoint = $segments[1] ?? '';
    
    switch ($endpoint) {
        case '':
            // List news
            if ($method !== 'GET') {
                sendResponse(405, ['error' => 'Method not allowed']);
            }
            
            $page = (int)($_GET['page'] ?? 1);
            $limit = min((int)($_GET['limit'] ?? 20), 100);
            $offset = ($page - 1) * $limit;
            $category = $_GET['category'] ?? '';
            $search = $_GET['search'] ?? '';
            
            $whereClauses = ['is_published = 1'];
            $params = [];
            
            if ($category) {
                $whereClauses[] = 'category_id = (SELECT id FROM categories WHERE slug = ?)';
                $params[] = $category;
            }
            
            if ($search) {
                $whereClauses[] = '(title_news LIKE ? OR text_news LIKE ?)';
                $searchParam = '%' . $search . '%';
                $params[] = $searchParam;
                $params[] = $searchParam;
            }
            
            $whereClause = implode(' AND ', $whereClauses);
            
            $news = db_fetch_all("
                SELECT n.*, c.name as category_name, c.slug as category_slug
                FROM news n
                LEFT JOIN categories c ON n.category_id = c.id
                WHERE $whereClause
                ORDER BY n.created_at DESC
                LIMIT $limit OFFSET $offset
            ", $params);
            
            $total = db_fetch_column("SELECT COUNT(*) FROM news WHERE $whereClause", $params);
            
            sendResponse(200, [
                'success' => true,
                'data' => $news,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $limit,
                    'total' => $total,
                    'total_pages' => ceil($total / $limit)
                ]
            ]);
            break;
            
        default:
            // Get single news item
            if ($method !== 'GET' || !is_numeric($endpoint)) {
                sendResponse(404, ['error' => 'News item not found']);
            }
            
            $newsItem = db_fetch_one("
                SELECT n.*, c.name as category_name, c.slug as category_slug
                FROM news n
                LEFT JOIN categories c ON n.category_id = c.id
                WHERE n.id_news = ? AND n.is_published = 1
            ", [$endpoint]);
            
            if (!$newsItem) {
                sendResponse(404, ['error' => 'News item not found']);
            }
            
            // Update views
            db_query("UPDATE news SET views = views + 1 WHERE id_news = ?", [$endpoint]);
            
            // Check if user has favorited
            $isFavorited = db_fetch_column("
                SELECT COUNT(*) FROM favorites 
                WHERE user_id = ? AND item_type = 'news' AND item_id = ?
            ", [$user['id'], $endpoint]) > 0;
            
            // Get rating stats
            $ratingStats = db_fetch_one("
                SELECT COUNT(*) as count, AVG(rating) as average
                FROM ratings 
                WHERE item_type = 'news' AND item_id = ?
            ", [$endpoint]);
            
            $userRating = db_fetch_column("
                SELECT rating FROM ratings 
                WHERE user_id = ? AND item_type = 'news' AND item_id = ?
            ", [$user['id'], $endpoint]);
            
            sendResponse(200, [
                'success' => true,
                'data' => $newsItem,
                'user_data' => [
                    'is_favorited' => $isFavorited,
                    'user_rating' => $userRating ? (int)$userRating : null
                ],
                'stats' => [
                    'rating_count' => (int)$ratingStats['count'],
                    'average_rating' => $ratingStats['average'] ? round($ratingStats['average'], 1) : null
                ]
            ]);
    }
}

// Posts handlers
function handlePosts($segments, $method, $input, $user) {
    $endpoint = $segments[1] ?? '';
    
    switch ($endpoint) {
        case '':
            // List posts
            if ($method !== 'GET') {
                sendResponse(405, ['error' => 'Method not allowed']);
            }
            
            $page = (int)($_GET['page'] ?? 1);
            $limit = min((int)($_GET['limit'] ?? 20), 100);
            $offset = ($page - 1) * $limit;
            $category = $_GET['category'] ?? '';
            $search = $_GET['search'] ?? '';
            
            $whereClauses = ['is_published = 1'];
            $params = [];
            
            if ($category) {
                $whereClauses[] = 'category = (SELECT id FROM categories WHERE slug = ?)';
                $params[] = $category;
            }
            
            if ($search) {
                $whereClauses[] = '(title_post LIKE ? OR text_post LIKE ?)';
                $searchParam = '%' . $search . '%';
                $params[] = $searchParam;
                $params[] = $searchParam;
            }
            
            $whereClause = implode(' AND ', $whereClauses);
            
            $posts = db_fetch_all("
                SELECT p.*, c.name as category_name, c.slug as category_slug
                FROM posts p
                LEFT JOIN categories c ON p.category = c.id
                WHERE $whereClause
                ORDER BY p.date_post DESC
                LIMIT $limit OFFSET $offset
            ", $params);
            
            $total = db_fetch_column("SELECT COUNT(*) FROM posts WHERE $whereClause", $params);
            
            sendResponse(200, [
                'success' => true,
                'data' => $posts,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $limit,
                    'total' => $total,
                    'total_pages' => ceil($total / $limit)
                ]
            ]);
            break;
            
        default:
            // Get single post
            if ($method !== 'GET' || !is_numeric($endpoint)) {
                sendResponse(404, ['error' => 'Post not found']);
            }
            
            $post = db_fetch_one("
                SELECT p.*, c.name as category_name, c.slug as category_slug
                FROM posts p
                LEFT JOIN categories c ON p.category = c.id
                WHERE p.id = ? AND p.is_published = 1
            ", [$endpoint]);
            
            if (!$post) {
                sendResponse(404, ['error' => 'Post not found']);
            }
            
            // Update views
            db_query("UPDATE posts SET views = views + 1 WHERE id = ?", [$endpoint]);
            
            // Check if user has favorited
            $isFavorited = db_fetch_column("
                SELECT COUNT(*) FROM favorites 
                WHERE user_id = ? AND item_type = 'post' AND item_id = ?
            ", [$user['id'], $endpoint]) > 0;
            
            // Get rating stats
            $ratingStats = db_fetch_one("
                SELECT COUNT(*) as count, AVG(rating) as average
                FROM ratings 
                WHERE item_type = 'post' AND item_id = ?
            ", [$endpoint]);
            
            $userRating = db_fetch_column("
                SELECT rating FROM ratings 
                WHERE user_id = ? AND item_type = 'post' AND item_id = ?
            ", [$user['id'], $endpoint]);
            
            sendResponse(200, [
                'success' => true,
                'data' => $post,
                'user_data' => [
                    'is_favorited' => $isFavorited,
                    'user_rating' => $userRating ? (int)$userRating : null
                ],
                'stats' => [
                    'rating_count' => (int)$ratingStats['count'],
                    'average_rating' => $ratingStats['average'] ? round($ratingStats['average'], 1) : null
                ]
            ]);
    }
}

// Events handlers
function handleEvents($segments, $method, $input, $user) {
    $endpoint = $segments[1] ?? '';
    
    switch ($endpoint) {
        case '':
            // List events
            if ($method !== 'GET') {
                sendResponse(405, ['error' => 'Method not allowed']);
            }
            
            $page = (int)($_GET['page'] ?? 1);
            $limit = min((int)($_GET['limit'] ?? 20), 100);
            $offset = ($page - 1) * $limit;
            $eventType = $_GET['type'] ?? '';
            $targetAudience = $_GET['audience'] ?? '';
            $dateFilter = $_GET['date'] ?? 'upcoming';
            
            $whereClauses = ['is_public = 1'];
            $params = [];
            
            if ($eventType) {
                $whereClauses[] = 'event_type = ?';
                $params[] = $eventType;
            }
            
            if ($targetAudience) {
                $whereClauses[] = '(target_audience = ? OR target_audience = "all")';
                $params[] = $targetAudience;
            }
            
            // Date filtering
            $today = date('Y-m-d');
            switch ($dateFilter) {
                case 'today':
                    $whereClauses[] = 'start_date = ?';
                    $params[] = $today;
                    break;
                case 'this_week':
                    $weekEnd = date('Y-m-d', strtotime('+7 days'));
                    $whereClauses[] = 'start_date BETWEEN ? AND ?';
                    $params[] = $today;
                    $params[] = $weekEnd;
                    break;
                case 'upcoming':
                default:
                    $whereClauses[] = 'start_date >= ?';
                    $params[] = $today;
                    break;
            }
            
            $whereClause = implode(' AND ', $whereClauses);
            
            $events = db_fetch_all("
                SELECT * FROM events 
                WHERE $whereClause
                ORDER BY is_featured DESC, start_date ASC, start_time ASC
                LIMIT $limit OFFSET $offset
            ", $params);
            
            $total = db_fetch_column("SELECT COUNT(*) FROM events WHERE $whereClause", $params);
            
            sendResponse(200, [
                'success' => true,
                'data' => $events,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $limit,
                    'total' => $total,
                    'total_pages' => ceil($total / $limit)
                ]
            ]);
            break;
            
        case 'subscriptions':
            // Get user's event subscriptions
            if ($method !== 'GET') {
                sendResponse(405, ['error' => 'Method not allowed']);
            }
            
            $subscriptions = db_fetch_all("
                SELECT es.*, e.title, e.start_date, e.start_time, e.event_type, e.location
                FROM event_subscriptions es
                JOIN events e ON es.event_id = e.id
                WHERE es.user_id = ? AND e.start_date >= CURRENT_DATE
                ORDER BY e.start_date ASC, e.start_time ASC
            ", [$user['id']]);
            
            sendResponse(200, [
                'success' => true,
                'data' => $subscriptions
            ]);
            break;
            
        default:
            // Get single event
            if ($method !== 'GET' || !is_numeric($endpoint)) {
                sendResponse(404, ['error' => 'Event not found']);
            }
            
            $event = db_fetch_one("SELECT * FROM events WHERE id = ? AND is_public = 1", [$endpoint]);
            
            if (!$event) {
                sendResponse(404, ['error' => 'Event not found']);
            }
            
            // Update views
            db_query("UPDATE events SET views = views + 1 WHERE id = ?", [$endpoint]);
            
            // Check if user is subscribed
            $isSubscribed = db_fetch_column("
                SELECT COUNT(*) FROM event_subscriptions 
                WHERE user_id = ? AND event_id = ?
            ", [$user['id'], $endpoint]) > 0;
            
            sendResponse(200, [
                'success' => true,
                'data' => $event,
                'user_data' => [
                    'is_subscribed' => $isSubscribed
                ]
            ]);
    }
}

// Schools, VPO, SPO handlers
function handleSchools($segments, $method, $input, $user) {
    handleInstitutions('schools', 'name_school', $segments, $method, $input, $user);
}

function handleVpo($segments, $method, $input, $user) {
    handleInstitutions('vpo', 'name_vpo', $segments, $method, $input, $user);
}

function handleSpo($segments, $method, $input, $user) {
    handleInstitutions('spo', 'name_spo', $segments, $method, $input, $user);
}

function handleInstitutions($table, $nameField, $segments, $method, $input, $user) {
    $endpoint = $segments[1] ?? '';
    
    if ($method !== 'GET') {
        sendResponse(405, ['error' => 'Method not allowed']);
    }
    
    switch ($endpoint) {
        case '':
            // List institutions
            $page = (int)($_GET['page'] ?? 1);
            $limit = min((int)($_GET['limit'] ?? 20), 100);
            $offset = ($page - 1) * $limit;
            $region = $_GET['region'] ?? '';
            $search = $_GET['search'] ?? '';
            
            $whereClauses = [];
            $params = [];
            
            if ($region) {
                $whereClauses[] = 'region_id = ?';
                $params[] = $region;
            }
            
            if ($search) {
                $whereClauses[] = "($nameField LIKE ? OR description LIKE ?)";
                $searchParam = '%' . $search . '%';
                $params[] = $searchParam;
                $params[] = $searchParam;
            }
            
            $whereClause = $whereClauses ? 'WHERE ' . implode(' AND ', $whereClauses) : '';
            
            $institutions = db_fetch_all("
                SELECT * FROM $table 
                $whereClause
                ORDER BY $nameField
                LIMIT $limit OFFSET $offset
            ", $params);
            
            $total = db_fetch_column("SELECT COUNT(*) FROM $table $whereClause", $params);
            
            sendResponse(200, [
                'success' => true,
                'data' => $institutions,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $limit,
                    'total' => $total,
                    'total_pages' => ceil($total / $limit)
                ]
            ]);
            break;
            
        default:
            // Get single institution
            if (!is_numeric($endpoint)) {
                sendResponse(404, ['error' => 'Institution not found']);
            }
            
            $institution = db_fetch_one("SELECT * FROM $table WHERE id = ?", [$endpoint]);
            
            if (!$institution) {
                sendResponse(404, ['error' => 'Institution not found']);
            }
            
            sendResponse(200, [
                'success' => true,
                'data' => $institution
            ]);
    }
}

// Search handler
function handleSearch($segments, $method, $input, $user) {
    if ($method !== 'GET') {
        sendResponse(405, ['error' => 'Method not allowed']);
    }
    
    $query = $_GET['q'] ?? '';
    $type = $_GET['type'] ?? 'all'; // all, news, posts, events, schools, vpo, spo
    $limit = min((int)($_GET['limit'] ?? 20), 100);
    
    if (empty($query)) {
        sendResponse(400, ['error' => 'Search query required']);
    }
    
    $results = [];
    $searchParam = '%' . $query . '%';
    
    if (in_array($type, ['all', 'news'])) {
        $news = db_fetch_all("
            SELECT 'news' as type, id_news as id, title_news as title, 
                   text_news as excerpt, created_at as date
            FROM news 
            WHERE is_published = 1 AND (title_news LIKE ? OR text_news LIKE ?)
            ORDER BY created_at DESC
            LIMIT ?
        ", [$searchParam, $searchParam, $limit]);
        
        $results['news'] = $news;
    }
    
    if (in_array($type, ['all', 'posts'])) {
        $posts = db_fetch_all("
            SELECT 'post' as type, id, title_post as title, 
                   text_post as excerpt, date_post as date
            FROM posts 
            WHERE is_published = 1 AND (title_post LIKE ? OR text_post LIKE ?)
            ORDER BY date_post DESC
            LIMIT ?
        ", [$searchParam, $searchParam, $limit]);
        
        $results['posts'] = $posts;
    }
    
    if (in_array($type, ['all', 'events'])) {
        $events = db_fetch_all("
            SELECT 'event' as type, id, title, description as excerpt, 
                   start_date as date, event_type, location
            FROM events 
            WHERE is_public = 1 AND (title LIKE ? OR description LIKE ?)
            ORDER BY start_date ASC
            LIMIT ?
        ", [$searchParam, $searchParam, $limit]);
        
        $results['events'] = $events;
    }
    
    if (in_array($type, ['all', 'schools'])) {
        $schools = db_fetch_all("
            SELECT 'school' as type, id, name_school as title, 
                   description as excerpt
            FROM schools 
            WHERE name_school LIKE ? OR description LIKE ?
            ORDER BY name_school
            LIMIT ?
        ", [$searchParam, $searchParam, $limit]);
        
        $results['schools'] = $schools;
    }
    
    sendResponse(200, [
        'success' => true,
        'query' => $query,
        'results' => $results
    ]);
}
?>