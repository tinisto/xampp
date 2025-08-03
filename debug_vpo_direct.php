<?php
// Direct debug for VPO page issue
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Debug VPO All Regions</h1>";

// Test if content file exists
$contentFile = $_SERVER['DOCUMENT_ROOT'] . '/pages/common/educational-institutions-all-regions/educational-institutions-all-regions-content-fixed.php';
echo "<p>Content file path: " . $contentFile . "</p>";
echo "<p>File exists: " . (file_exists($contentFile) ? 'YES' : 'NO') . "</p>";

if (!file_exists($contentFile)) {
    echo "<p style='color: red;'>ERROR: Content file not found!</p>";
    exit;
}

// Test database connection
try {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
    
    if (defined('DB_HOST') && defined('DB_USER') && defined('DB_PASS') && defined('DB_NAME')) {
        $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($connection->connect_error) {
            echo "<p style='color: red;'>Database connection failed: " . $connection->connect_error . "</p>";
        } else {
            echo "<p style='color: green;'>Database connected successfully</p>";
            $connection->set_charset("utf8mb4");
        }
    } else {
        echo "<p style='color: red;'>Database constants not defined</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Database error: " . $e->getMessage() . "</p>";
}

// Test content generation directly
echo "<hr><h2>Direct Content Generation Test:</h2>";

// Set required variables like the template engine would
$type = 'vpo';
$table = 'vpo';
$linkPrefix = '/vpo-in-region';
$pageTitle = 'ВПО по регионам';

// Try to include the content directly
ob_start();
try {
    include $contentFile;
    $content = ob_get_clean();
    if (empty(trim($content))) {
        echo "<p style='color: red;'>Content is empty!</p>";
    } else {
        echo "<p style='color: green;'>Content generated successfully (" . strlen($content) . " characters)</p>";
        echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
        echo $content;
        echo "</div>";
    }
} catch (Exception $e) {
    ob_end_clean();
    echo "<p style='color: red;'>Error generating content: " . $e->getMessage() . "</p>";
}

// Test URL parameters
echo "<hr><h2>URL Test:</h2>";
echo "<p>Current URL: " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p>\$_GET['type']: " . ($_GET['type'] ?? 'not set') . "</p>";

?>