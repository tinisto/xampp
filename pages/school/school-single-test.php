<?php
// Test version to check if template engine is working
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Handle slug-based URLs only
$url_slug = $_GET['url_slug'] ?? null;

if (!$url_slug) {
    header("Location: /404");
    exit();
}

$query = "SELECT s.*, r.region_name, r.region_name_en, t.town_name, t.town_name_en 
          FROM schools s
          LEFT JOIN regions r ON s.region_id = r.region_id
          LEFT JOIN towns t ON s.town_id = t.town_id
          WHERE s.url_slug = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("s", $url_slug);

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: /404");
    exit();
}

$row = $result->fetch_assoc();
$pageTitle = $row['name'] ?? 'Школа';

// Build location info for badge
$locationParts = array_filter([
    $row['town_name'] ?? '',
    $row['region_name'] ?? ''
]);
$locationText = implode(', ', $locationParts);

// Test if template engine exists
if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-ultimate.php')) {
    die("ERROR: template-engine-ultimate.php not found");
}

// Test with minimal content first
$content = '<div style="padding: 40px;"><h1>Test: ' . htmlspecialchars($pageTitle) . '</h1><p>Location: ' . htmlspecialchars($locationText) . '</p></div>';

// Use the template engine
$templateConfig = [
    'pageTitle' => $pageTitle,
    'pageHeader' => [
        'title' => $pageTitle,
        'showSearch' => false,
        'badge' => $locationText
    ],
    'content' => $content
];

require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-ultimate.php';

$stmt->close();
$connection->close();
?>