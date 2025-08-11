<?php
// Modern index.php with routing
require_once __DIR__ . '/database/db_modern.php';

// Get request URI
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Remove trailing slash except for root
if ($requestUri !== '/' && substr($requestUri, -1) === '/') {
    header('Location: ' . rtrim($requestUri, '/'), true, 301);
    exit;
}

// Simple router
$routes = [
    '/' => 'home',
    '/news' => 'news-list',
    '/news/novost-(\d+)' => 'news-single',
    '/news/([a-zA-Z0-9_-]+)' => 'news-single-slug',
    '/posts' => 'posts-list',
    '/post/statya-(\d+)' => 'post-single',
    '/post/([a-zA-Z0-9_-]+)' => 'post-single-slug',
    '/vpo' => 'vpo-list',
    '/vpo/([a-zA-Z0-9_-]+)' => 'vpo-single',
    '/spo' => 'spo-list',
    '/spo/([a-zA-Z0-9_-]+)' => 'spo-single',
    '/schools' => 'schools-list',
    '/school/([a-zA-Z0-9_-]+)' => 'school-single',
    '/tests' => 'tests-list',
    '/search' => 'search',
    '/login' => 'login',
    '/register' => 'register',
    '/welcome' => 'welcome',
    '/logout' => 'logout',
    '/profile' => 'profile',
    '/favorites' => 'favorites',
    '/settings' => 'settings',
    '/reading-lists' => 'reading-lists',
    '/reading-list/(\d+)' => 'reading-list-single',
    '/notifications' => 'notifications',
    '/recommendations' => 'recommendations',
    '/privacy' => 'privacy',
    '/admin' => 'admin',
    '/analytics' => 'analytics',
    '/sitemap.xml' => 'sitemap',
    '/rss.xml' => 'rss',
    '/api/favorites/toggle' => 'api-favorites-toggle',
    '/api/favorites/remove' => 'api-favorites-remove',
    '/api/comments/add' => 'api-comments-add',
    '/api/comments/delete' => 'api-comments-delete',
    '/api/rating/submit' => 'api-rating-submit',
    '/api/rating/get' => 'api-rating-get',
    '/api/reading-lists/quick-add' => 'api-reading-lists-quick-add',
    '/api/reading-lists/add-to-list' => 'api-reading-lists-add',
    '/api/reading-lists/get-user-lists' => 'api-reading-lists-get',
    '/api/reading-lists/create' => 'api-reading-lists-create',
    '/api/notifications/check' => 'api-notifications-check',
    '/api/notifications/mark-read' => 'api-notifications-mark-read',
    '/api/notifications/mark-all-read' => 'api-notifications-mark-all-read',
    '/api/notifications/get-recent' => 'api-notifications-get-recent',
    '/events' => 'events-list',
    '/events/calendar' => 'events-calendar',
    '/event/add' => 'event-add',
    '/event/(\\d+)' => 'event-single',
    '/event/(\\d+)/edit' => 'event-edit',
    '/api/events/subscribe' => 'api-events-subscribe',
    '/api/events/unsubscribe' => 'api-events-unsubscribe',
    '/api/events/get-subscriptions' => 'api-events-get-subscriptions',
    '/api/events/update-reminder' => 'api-events-update-reminder',
    '/api/events/check-upcoming' => 'api-events-check-upcoming',
    '/api/events/get-calendar-events' => 'api-events-get-calendar-events',
    '/api/analytics' => 'api-analytics',
    '/api/v1/docs' => 'api-docs',
    '/api/v1/.*' => 'api-v1',
];

// Check routes
$page = null;
$params = [];

foreach ($routes as $pattern => $handler) {
    $regex = '#^' . str_replace('/', '\/', $pattern) . '$#';
    if (preg_match($regex, $requestUri, $matches)) {
        $page = $handler;
        $params = array_slice($matches, 1);
        break;
    }
}

// Handle different pages
switch ($page) {
    case 'home':
        include 'home_modern.php';
        break;
        
    case 'news-list':
        include 'news_modern.php';
        break;
        
    case 'news-single':
        $_GET['id'] = $params[0];
        include 'news-single.php';
        break;
        
    case 'news-single-slug':
        $_GET['slug'] = $params[0];
        include 'news-single.php';
        break;
        
    case 'posts-list':
        include 'posts_modern.php';
        break;
        
    case 'post-single':
        $_GET['id'] = $params[0];
        include 'post-single.php';
        break;
        
    case 'post-single-slug':
        $_GET['slug'] = $params[0];
        include 'post-single.php';
        break;
        
    case 'vpo-list':
        include 'vpo_modern.php';
        break;
        
    case 'vpo-single':
        $_GET['slug'] = $params[0];
        include 'vpo-single.php';
        break;
        
    case 'spo-list':
        include 'spo_modern.php';
        break;
        
    case 'spo-single':
        $_GET['slug'] = $params[0];
        include 'spo-single.php';
        break;
        
    case 'schools-list':
        include 'schools_modern.php';
        break;
        
    case 'tests-list':
        include 'tests-new.php';
        break;
        
    case 'school-single':
        $_GET['slug'] = $params[0];
        include 'school-single.php';
        break;
        
    case 'search':
        include 'search_modern.php';
        break;
        
    case 'login':
        include 'login_modern.php';
        break;
        
    case 'register':
        include 'register_modern.php';
        break;
        
    case 'welcome':
        include 'welcome_modern.php';
        break;
        
    case 'logout':
        include 'logout_modern.php';
        break;
        
    case 'profile':
        include 'profile_modern.php';
        break;
        
    case 'favorites':
        include 'favorites_modern.php';
        break;
        
    case 'settings':
        include 'settings_modern.php';
        break;
        
    case 'reading-lists':
        include 'reading-lists.php';
        break;
        
    case 'reading-list-single':
        $_GET['id'] = $params[0];
        include 'reading-list-single.php';
        break;
        
    case 'notifications':
        include 'notifications.php';
        break;
        
    case 'recommendations':
        include 'recommendations.php';
        break;
        
    case 'privacy':
        include 'privacy_modern.php';
        break;
        
    case 'admin':
        include 'admin/index.php';
        break;
        
    case 'analytics':
        include 'analytics.php';
        break;
        
    case 'sitemap':
        include 'sitemap.php';
        break;
        
    case 'rss':
        include 'rss.php';
        break;
        
    case 'api-favorites-toggle':
        $_GET['action'] = 'toggle';
        include 'api_favorites.php';
        break;
        
    case 'api-favorites-remove':
        $_GET['action'] = 'remove';
        include 'api_favorites.php';
        break;
        
    case 'api-comments-add':
        $_GET['action'] = 'add';
        include 'api_comments.php';
        break;
        
    case 'api-comments-delete':
        $_GET['action'] = 'delete';
        include 'api_comments.php';
        break;
        
    case 'api-rating-submit':
        $_GET['action'] = 'submit';
        include 'api_rating.php';
        break;
        
    case 'api-rating-get':
        $_GET['action'] = 'get';
        include 'api_rating.php';
        break;
        
    case 'api-reading-lists-quick-add':
        $_GET['action'] = 'quick_add';
        include 'api_reading_lists.php';
        break;
        
    case 'api-reading-lists-add':
        $_GET['action'] = 'add_to_list';
        include 'api_reading_lists.php';
        break;
        
    case 'api-reading-lists-get':
        $_GET['action'] = 'get_user_lists';
        include 'api_reading_lists.php';
        break;
        
    case 'api-reading-lists-create':
        $_GET['action'] = 'create_list';
        include 'api_reading_lists.php';
        break;
        
    case 'api-notifications-check':
        $_GET['action'] = 'check';
        include 'api_notifications.php';
        break;
        
    case 'api-notifications-mark-read':
        $_GET['action'] = 'mark_read';
        include 'api_notifications.php';
        break;
        
    case 'api-notifications-mark-all-read':
        $_GET['action'] = 'mark_all_read';
        include 'api_notifications.php';
        break;
        
    case 'api-notifications-get-recent':
        $_GET['action'] = 'get_recent';
        include 'api_notifications.php';
        break;
        
    case 'events-list':
        include 'events.php';
        break;
        
    case 'events-calendar':
        include 'events-calendar.php';
        break;
        
    case 'event-add':
        include 'event-add.php';
        break;
        
    case 'event-single':
        $_GET['id'] = $params[0];
        include 'event-single.php';
        break;
        
    case 'event-edit':
        $_GET['id'] = $params[0];
        include 'event-edit.php';
        break;
        
    case 'api-events-subscribe':
        $_GET['action'] = 'subscribe';
        include 'api_events.php';
        break;
        
    case 'api-events-unsubscribe':
        $_GET['action'] = 'unsubscribe';
        include 'api_events.php';
        break;
        
    case 'api-events-get-subscriptions':
        $_GET['action'] = 'get_subscriptions';
        include 'api_events.php';
        break;
        
    case 'api-events-update-reminder':
        $_GET['action'] = 'update_reminder';
        include 'api_events.php';
        break;
        
    case 'api-events-check-upcoming':
        $_GET['action'] = 'check_upcoming';
        include 'api_events.php';
        break;
        
    case 'api-events-get-calendar-events':
        $_GET['action'] = 'get_calendar_events';
        include 'api_events.php';
        break;
        
    case 'api-analytics':
        include 'api_analytics.php';
        break;
        
    case 'api-docs':
        include 'api/v1/docs.php';
        break;
        
    case 'api-v1':
        include 'api/v1/index.php';
        break;
        
    default:
        // 404 page
        header('HTTP/1.0 404 Not Found');
        include '404_modern.php';
        break;
}
?>