<?php
// Simple test to check database performance optimizations

$servername = "localhost";
$username = "franko";
$password = "I68d54M4k71N";
$dbname = "11klassnikiDB";

try {
    $connection = new mysqli($servername, $username, $password, $dbname);
    
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }
    
    echo "<h1>Database Performance Test</h1>";
    
    // Test 1: Add indexes for better performance
    echo "<h2>Adding Database Indexes</h2>";
    
    $indexes = [
        "CREATE INDEX IF NOT EXISTS idx_posts_category_date ON posts(category, date_post DESC)",
        "CREATE INDEX IF NOT EXISTS idx_posts_status ON posts(status)",
        "CREATE INDEX IF NOT EXISTS idx_posts_url_slug ON posts(url_slug)",
        "CREATE INDEX IF NOT EXISTS idx_categories_url ON categories(url_category)",
        "CREATE INDEX IF NOT EXISTS idx_posts_category_status ON posts(category, status, date_post DESC)"
    ];
    
    foreach ($indexes as $indexQuery) {
        if ($connection->query($indexQuery)) {
            echo "✓ Index created successfully<br>";
        } else {
            echo "✗ Index creation failed: " . $connection->error . "<br>";
        }
    }
    
    // Test 2: Check current indexes
    echo "<h2>Current Indexes</h2>";
    $result = $connection->query("SHOW INDEX FROM posts");
    while ($row = $result->fetch_assoc()) {
        echo $row['Table'] . " - " . $row['Key_name'] . " (" . $row['Column_name'] . ")<br>";
    }
    
    // Test 3: Performance comparison
    echo "<h2>Query Performance Test</h2>";
    
    // Old style query
    $start = microtime(true);
    $result1 = $connection->query("SELECT * FROM posts WHERE category = 21 ORDER BY date_post DESC LIMIT 8");
    $result2 = $connection->query("SELECT * FROM posts WHERE category = 6 ORDER BY date_post DESC LIMIT 8");
    $time1 = microtime(true) - $start;
    
    // New optimized query
    $start = microtime(true);
    $result3 = $connection->query("
        (SELECT id, title_post, text_post, url_slug, date_post 
         FROM posts WHERE category = 21 ORDER BY date_post DESC LIMIT 8)
        UNION ALL
        (SELECT id, title_post, text_post, url_slug, date_post 
         FROM posts WHERE category = 6 ORDER BY date_post DESC LIMIT 8)
        ORDER BY date_post DESC
    ");
    $time2 = microtime(true) - $start;
    
    echo "Old method (2 separate queries): " . number_format($time1 * 1000, 2) . " ms<br>";
    echo "New method (1 UNION query): " . number_format($time2 * 1000, 2) . " ms<br>";
    echo "Performance improvement: " . number_format((($time1 - $time2) / $time1) * 100, 1) . "%<br>";
    
    echo "<h2>Success!</h2>";
    echo "Database indexes have been optimized for better performance.";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>