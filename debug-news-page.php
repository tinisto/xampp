<?php
echo "<h3>News Page Debug</h3>";
echo "<p><strong>REQUEST_URI:</strong> " . ($_SERVER['REQUEST_URI'] ?? 'NOT SET') . "</p>";
echo "<p><strong>SCRIPT_NAME:</strong> " . ($_SERVER['SCRIPT_NAME'] ?? 'NOT SET') . "</p>";
echo "<p><strong>PHP_SELF:</strong> " . ($_SERVER['PHP_SELF'] ?? 'NOT SET') . "</p>";

echo "<h4>GET Parameters:</h4>";
if (empty($_GET)) {
    echo "<p>No GET parameters</p>";
} else {
    foreach ($_GET as $key => $value) {
        echo "<p><strong>{$key}:</strong> {$value}</p>";
    }
}

echo "<h4>File Detection:</h4>";
$currentFile = __FILE__;
echo "<p><strong>Current file:</strong> {$currentFile}</p>";

// Check if we can find the news.php file
$newsFile = $_SERVER['DOCUMENT_ROOT'] . '/pages/common/news/news.php';
if (file_exists($newsFile)) {
    echo "<p>✅ News file exists: {$newsFile}</p>";
    echo "<p><strong>Last modified:</strong> " . date('Y-m-d H:i:s', filemtime($newsFile)) . "</p>";
} else {
    echo "<p>❌ News file not found: {$newsFile}</p>";
}

// Check .htaccess routing
echo "<h4>URL Routing Check:</h4>";
$uri = $_SERVER['REQUEST_URI'] ?? '';
if (strpos($uri, '/news') === 0) {
    echo "<p>✅ URL starts with /news</p>";
    
    // Check for specific patterns
    if (preg_match('#^/news/([^/]+)/?$#', $uri, $matches)) {
        echo "<p>✅ Matches news category pattern: {$matches[1]}</p>";
        echo "<p>Should set url_news = {$matches[1]}</p>";
    } elseif ($uri === '/news' || $uri === '/news/') {
        echo "<p>✅ Main news page</p>";
    } else {
        echo "<p>⚠️ Unknown news URL pattern</p>";
    }
} else {
    echo "<p>❌ URL does not start with /news</p>";
}

// Check database connection
echo "<h4>Database Check:</h4>";
try {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
    
    if (isset($connection) && $connection) {
        echo "<p>✅ Database connection exists</p>";
        
        // Quick check for news in spo category
        $spoCount = mysqli_query($connection, "SELECT COUNT(*) as count FROM news WHERE news_type = 'spo' AND status = 'published'");
        if ($spoCount) {
            $result = mysqli_fetch_assoc($spoCount);
            echo "<p><strong>SPO news count:</strong> {$result['count']}</p>";
        }
        
        // Quick check for all news
        $allCount = mysqli_query($connection, "SELECT COUNT(*) as count FROM news WHERE status = 'published'");
        if ($allCount) {
            $result = mysqli_fetch_assoc($allCount);
            echo "<p><strong>Total news count:</strong> {$result['count']}</p>";
        }
    } else {
        echo "<p>❌ No database connection</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Database error: " . $e->getMessage() . "</p>";
}
?>