<?php
// Simple search process - redirect to search page with query
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

// Get search query
$query = $_GET['query'] ?? '';

// Basic validation
if (empty($query)) {
    header('Location: /');
    exit;
}

// Sanitize the query
$query = trim($query);
if (strlen($query) < 2) {
    header('Location: /?error=query_too_short');
    exit;
}

if (strlen($query) > 100) {
    header('Location: /?error=query_too_long');
    exit;
}

// Redirect to search results page
header('Location: /search?' . http_build_query(['query' => $query]));
exit;
?>