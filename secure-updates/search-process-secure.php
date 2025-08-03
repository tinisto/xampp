<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../database/db_connections.php';
require_once __DIR__ . '/../common-components/check_under_construction.php';

// Initialize database
$db = Database::getInstance($connection);

// Check if a search query is set and not empty
if (isset($_GET['query']) && !empty($_GET['query'])) {
    $searchQuery = trim($_GET['query']);
    
    // Validate search query - allow Cyrillic, Latin, numbers, and spaces
    if (!preg_match("/^[\p{L}0-9\s]+$/u", $searchQuery)) {
        // Log suspicious activity
        ErrorHandler::log('Suspicious search attempt', 'warning', [
            'query' => $searchQuery,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ]);
        
        die("Invalid search input.");
    }
    
    // Additional validation - check length
    if (strlen($searchQuery) < 2) {
        die("Search query too short. Please enter at least 2 characters.");
    }
    
    if (strlen($searchQuery) > 100) {
        die("Search query too long. Please enter less than 100 characters.");
    }
    
    // Get user info if logged in
    $userId = $_SESSION['user_id'] ?? null;
    $userEmail = $_SESSION['email'] ?? null;
    
    // Log the search query
    try {
        $db->insert('search_logs', [
            'search_query' => $searchQuery,
            'user_email' => $userEmail,
            'user_id' => $userId,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    } catch (Exception $e) {
        // Log error but don't stop search
        ErrorHandler::log('Failed to log search query: ' . $e->getMessage(), 'error');
    }
    
    // Log search activity
    ErrorHandler::log('Search performed', 'info', [
        'query' => $searchQuery,
        'user_id' => $userId
    ]);
    
    // Prepare data for template
    $mainContent = 'search-content-secure.php';
    $pageTitle = 'Поиск - 11-классники';
    $additionalData = ['searchQuery' => $searchQuery];
    
    include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-ultimate.php';
    
// Template configuration
$templateConfig = [
    'layoutType' => 'default',
    'cssFramework' => 'custom',
    'headerType' => 'modern',
    'footerType' => 'modern',
    'darkMode' => true
];

renderTemplate($pageTitle, $mainContent, $templateConfig);
} else {
    // No search query - redirect to search page
    header('Location: /search');
    exit();
}