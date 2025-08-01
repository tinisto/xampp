<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>News Routing Test</h1>";

// Test the .htaccess rewrite rule
echo "<h2>.htaccess Rule Analysis:</h2>";
echo "<p>The rule: <code>RewriteRule ^news/([^/]+)$ pages/common/news/news.php?url_news=\$1 [QSA,NC,L]</code></p>";

$testUrls = [
    'prodleniye-novogodnikh-kanikul-v-shkolakh-belgorodskoy-oblasti',
    'test-news-url',
    'another-news-article'
];

echo "<h2>Testing URLs:</h2>";
foreach ($testUrls as $url) {
    echo "<p><strong>URL:</strong> /news/" . htmlspecialchars($url) . "</p>";
    echo "<p>→ Should map to: pages/common/news/news.php?url_news=" . htmlspecialchars($url) . "</p>";
    echo "<hr>";
}

// Check if news.php is accessible
$newsFile = $_SERVER['DOCUMENT_ROOT'] . '/pages/common/news/news.php';
echo "<h2>File Accessibility:</h2>";
echo "<p>News file path: " . htmlspecialchars($newsFile) . "</p>";
echo "<p>File exists: " . (file_exists($newsFile) ? "✓ Yes" : "✗ No") . "</p>";
echo "<p>File readable: " . (is_readable($newsFile) ? "✓ Yes" : "✗ No") . "</p>";

// Test database connection
echo "<h2>Database Connection Test:</h2>";
try {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
    if ($connection) {
        echo "<p>✓ Database connection successful</p>";
        
        // Test if news table exists
        $query = "SHOW TABLES LIKE 'news'";
        $result = mysqli_query($connection, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            echo "<p>✓ News table exists</p>";
            
            // Count news articles
            $countQuery = "SELECT COUNT(*) as count FROM news";
            $countResult = mysqli_query($connection, $countQuery);
            if ($countResult) {
                $count = mysqli_fetch_assoc($countResult);
                echo "<p>✓ Found " . $count['count'] . " news articles in database</p>";
            }
        } else {
            echo "<p>✗ News table not found</p>";
        }
    } else {
        echo "<p>✗ Database connection failed</p>";
    }
} catch (Exception $e) {
    echo "<p>✗ Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Test template engine
echo "<h2>Template Engine Test:</h2>";
$templateFile = $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-ultimate.php';
echo "<p>Template file path: " . htmlspecialchars($templateFile) . "</p>";
echo "<p>Template exists: " . (file_exists($templateFile) ? "✓ Yes" : "✗ No") . "</p>";
echo "<p>Template readable: " . (is_readable($templateFile) ? "✓ Yes" : "✗ No") . "</p>";

// Test header file
echo "<h2>Header File Test:</h2>";
$headerFile = $_SERVER['DOCUMENT_ROOT'] . '/common-components/header-unified-simple-safe-v2.php';
echo "<p>Header file path: " . htmlspecialchars($headerFile) . "</p>";
echo "<p>Header exists: " . (file_exists($headerFile) ? "✓ Yes" : "✗ No") . "</p>";
echo "<p>Header readable: " . (is_readable($headerFile) ? "✓ Yes" : "✗ No") . "</p>";

echo "<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1, h2 { color: #333; }
code { background: #f4f4f4; padding: 2px 4px; }
hr { margin: 20px 0; }
</style>";
?>