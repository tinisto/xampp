<?php
/**
 * Main routing handler for all requests
 * This file handles URL routing for the application
 */

// Include security headers first
require_once __DIR__ . '/includes/security-headers.php';

// Start session
require_once __DIR__ . '/session-init.php';

// Get the requested URI and clean it
$requestUri = $_SERVER['REQUEST_URI'];
$path = parse_url($requestUri, PHP_URL_PATH);
$path = trim($path, '/');

// Parse query parameters
parse_str(parse_url($requestUri, PHP_URL_QUERY) ?: '', $queryParams);

// Define route mappings
$routes = [
    // Main navigation pages
    '' => '/index.php',
    'news' => '/news.php',
    'posts' => '/posts.php', // This will need to be created
    'tests' => '/tests.php', // This will need to be created
    
    // Educational institutions
    'vpo' => '/vpo.php',
    'spo' => '/spo.php', 
    'schools' => '/schools.php',
    'vpo-all-regions' => '/educational-institutions-all-regions-real.php?institution_type=vpo',
    'spo-all-regions' => '/educational-institutions-all-regions-real.php?institution_type=spo',
    'schools-all-regions' => '/educational-institutions-all-regions-real.php?institution_type=school',
    
    // User management
    'login' => '/login.php',
    'logout' => '/logout.php',
    'registration' => '/registration.php',
    'account' => '/account.php',
    'profile' => '/user-profile.php',
    
    // Other pages
    'about' => '/about.php',
    'contact' => '/contact.php',
    'privacy' => '/privacy.php',
    'terms' => '/terms.php',
    'events' => '/events.php',
    
    // Admin routes
    'admin' => '/admin/index.php',
];

// Handle dynamic routes
if (empty($path)) {
    // Home page
    require __DIR__ . '/index.php';
    exit;
}

// Check for direct route match
if (isset($routes[$path])) {
    $targetFile = __DIR__ . $routes[$path];
    if (file_exists($targetFile)) {
        require $targetFile;
        exit;
    }
}

// Handle category routes like /category/something
if (preg_match('#^category/(.+)$#', $path, $matches)) {
    $_GET['url_category'] = $matches[1];
    require __DIR__ . '/category.php';
    exit;
}

// Handle news routes like /news/something
if (preg_match('#^news/(.+)$#', $path, $matches)) {
    $_GET['url_news'] = $matches[1];
    require __DIR__ . '/news.php';
    exit;
}

// Handle posts routes like /post/something or /posts/something
if (preg_match('#^posts?/(.+)$#', $path, $matches)) {
    $_GET['url_post'] = $matches[1];
    require __DIR__ . '/post.php';
    exit;
}

// Handle school routes
if (preg_match('#^school/(.+)$#', $path, $matches)) {
    $_GET['url_slug'] = $matches[1];
    require __DIR__ . '/school-single.php';
    exit;
}

// Handle SPO routes  
if (preg_match('#^spo/(.+)$#', $path, $matches)) {
    $_GET['url_slug'] = $matches[1];
    require __DIR__ . '/spo-single.php';
    exit;
}

// Handle VPO routes
if (preg_match('#^vpo/(.+)$#', $path, $matches)) {
    $_GET['url_slug'] = $matches[1];
    require __DIR__ . '/vpo-single.php';
    exit;
}

// Handle test routes like /test/123
if (preg_match('#^test/(\d+)$#', $path, $matches)) {
    $_GET['test_id'] = $matches[1];
    require __DIR__ . '/test-single.php';
    exit;
}

// Handle regional routes
if (preg_match('#^schools-in/(.+)$#', $path, $matches)) {
    $_GET['region'] = $matches[1];
    require __DIR__ . '/schools-in-region.php';
    exit;
}

if (preg_match('#^spo-in/(.+)$#', $path, $matches)) {
    $_GET['region'] = $matches[1];
    require __DIR__ . '/spo-in-region.php';
    exit;
}

if (preg_match('#^vpo-in/(.+)$#', $path, $matches)) {
    $_GET['region'] = $matches[1];
    require __DIR__ . '/vpo-in-region.php';
    exit;
}

// If no route matches, show 404
http_response_code(404);
require __DIR__ . '/404.php';
?>