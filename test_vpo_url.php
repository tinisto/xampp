<?php
// Test URL parameters for VPO pages
echo "<h1>URL Parameter Test</h1>";
echo "<p>Request URI: " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p>Query String: " . $_SERVER['QUERY_STRING'] . "</p>";
echo "<p>\$_GET array:</p>";
echo "<pre>";
print_r($_GET);
echo "</pre>";

// Test the specific parameter
echo "<p>type parameter: " . ($_GET['type'] ?? 'NOT SET') . "</p>";

// Test what the main file would do
$type = $_GET['type'] ?? 'schools';
echo "<p>Resolved type: " . $type . "</p>";

switch ($type) {
    case 'spo':
        $table = 'spo';
        $pageTitle = 'СПО по регионам';
        break;
    case 'vpo':
        $table = 'vpo';
        $pageTitle = 'ВПО по регионам';
        break;
    default:
        $table = 'schools';
        $pageTitle = 'Школы по регионам';
        break;
}

echo "<p>Table: " . $table . "</p>";
echo "<p>Page Title: " . $pageTitle . "</p>";
?>