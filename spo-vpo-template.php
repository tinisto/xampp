<?php
// SPO/VPO Template - Uses unified template system
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Get institution data to determine page title
$url = $_GET['url_slug'] ?? basename($_SERVER['REQUEST_URI']);
$url = preg_replace('/\?.*/', '', $url); // Remove query string

// Determine the type from URL parameter or URL path
$type = $_GET['type'] ?? null;
if (!$type) {
    // Fallback: determine from URL path
    $requestUri = $_SERVER['REQUEST_URI'];
    $type = strpos($requestUri, '/vpo/') !== false ? 'vpo' : 'spo';
}

// Quick query to get the institution name for page title
$query = "SELECT name FROM $type WHERE url_slug = ? LIMIT 1";
$stmt = $connection->prepare($query);
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

// Use the unified template system
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-ultimate.php';
renderTemplate($pageTitle, $mainContent, ['type' => $type, 'url_slug' => $url]);
?>