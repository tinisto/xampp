<?php
// SPO/VPO Single Page - Uses unified template system with dynamic title
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Get institution data from URL slug
$url = $_GET['url_slug'] ?? basename($_SERVER['REQUEST_URI']);
$url = preg_replace('/\?.*/', '', $url); // Remove query string

// Determine the type from URL parameter or URL path
$type = $_GET['type'] ?? null;
if (!$type) {
    // Fallback: determine from URL path
    $requestUri = $_SERVER['REQUEST_URI'];
    $type = strpos($requestUri, '/vpo/') !== false ? 'vpo' : 'spo';
}

// Validate type
if (!in_array($type, ['vpo', 'spo'])) {
    header("Location: /404");
    exit();
}

// Quick query to get the institution name for page title
$query = "SELECT name FROM $type WHERE url_slug = ? LIMIT 1";
$stmt = $connection->prepare($query);
if (!$stmt) {
    header("Location: /error");
    exit();
}

$stmt->bind_param("s", $url);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: /404");
    exit();
}

$row = $result->fetch_assoc();
$pageTitle = $row['name'] ?? 'Учебное заведение';
$stmt->close();

// Set up the main content path
$mainContent = '/pages/common/vpo-spo/spo-vpo-content.php';

// Pass data to the content template
$additionalData = [
    'type' => $type,
    'url_slug' => $url,
    'metaD' => $pageTitle . ' - информация об учебном заведении, контакты, администрация',
    'metaK' => $pageTitle . ', ' . ($type === 'vpo' ? 'вуз, университет' : 'колледж, техникум') . ', образование'
];

// Use the unified template system
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-ultimate.php';
renderTemplate($pageTitle, $mainContent, $additionalData);
?>