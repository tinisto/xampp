<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>htaccess Rewrite Test</h2>";

echo "<h3>Current URL: " . $_SERVER['REQUEST_URI'] . "</h3>";
echo "<h3>Script Name: " . $_SERVER['SCRIPT_NAME'] . "</h3>";

echo "<h3>All GET parameters:</h3>";
echo "<pre>";
print_r($_GET);
echo "</pre>";

echo "<h3>All SERVER variables:</h3>";
echo "<pre>";
foreach ($_SERVER as $key => $value) {
    if (strpos($key, 'REQUEST') !== false || strpos($key, 'QUERY') !== false || strpos($key, 'REDIRECT') !== false) {
        echo "$key = $value\n";
    }
}
echo "</pre>";

// Test if this is being called by a news URL
if (strpos($_SERVER['REQUEST_URI'], '/news/') !== false) {
    echo "<h3>✅ This appears to be a news URL</h3>";
    $urlParts = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
    if (count($urlParts) >= 2 && $urlParts[0] === 'news') {
        $newsSlug = $urlParts[1];
        echo "<h4>Extracted news slug: $newsSlug</h4>";
        
        // Test if this matches what should be in url_news
        if (isset($_GET['url_news'])) {
            echo "<h4>✅ url_news parameter is set: " . $_GET['url_news'] . "</h4>";
        } else {
            echo "<h4>❌ url_news parameter is NOT set - rewrite rule not working</h4>";
        }
    }
}
?>