<?php
// Initialize application
require_once __DIR__ . '/ErrorHandler.php';
require_once __DIR__ . '/Security.php';
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Cache.php';
require_once __DIR__ . '/SessionManager.php';
require_once __DIR__ . '/helpers.php';

// Load environment variables
$envFile = dirname(__DIR__) . '/.env';
if (file_exists($envFile)) {
    $debugMode = strpos(file_get_contents($envFile), 'APP_DEBUG=true') !== false;
} else {
    $debugMode = false;
}

// Initialize error handler
ErrorHandler::init($debugMode);

// Start session using centralized session manager
SessionManager::start();

// Initialize page caching for public pages
if (!isset($_SESSION['user_id']) && $_SERVER['REQUEST_METHOD'] === 'GET') {
    require_once __DIR__ . '/Cache.php';
    PageCache::start();
}

// Include CSRF middleware
require_once __DIR__ . '/csrf-middleware.php';

// Log page views
ErrorHandler::log('Page view', 'info', [
    'url' => $_SERVER['REQUEST_URI'] ?? 'Unknown',
    'ip' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown',
    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
    'user_id' => $_SESSION['user_id'] ?? null
]);