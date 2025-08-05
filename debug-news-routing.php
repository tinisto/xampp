<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>News Routing Debug</h2>";

// Test database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';

try {
    $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($connection->connect_error) {
        echo "Connection failed: " . $connection->connect_error . "<br>";
        exit();
    } else {
        echo "✅ Database connection successful<br>";
        $connection->set_charset("utf8mb4");
    }
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "<br>";
    exit();
}

// Check news table structure
echo "<h3>News Table Structure</h3>";
$structure = $connection->query("DESCRIBE news");
if ($structure) {
    echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
    while ($row = $structure->fetch_assoc()) {
        echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Null']}</td><td>{$row['Key']}</td></tr>";
    }
    echo "</table><br>";
} else {
    echo "❌ Could not describe news table<br>";
}

// Check for specific news URLs
$testUrls = [
    'letnoe-uchilische-v-krasnodare-popolnitsya-15-buduschimi-voennyimi-letchitsami',
    'novosti-obrazovaniya'
];

echo "<h3>Testing News URLs</h3>";
foreach ($testUrls as $url) {
    echo "<h4>Testing URL: $url</h4>";
    
    $query = "SELECT id_news, title_news, url_news FROM news WHERE url_slug = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("s", $url);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo "✅ Found: ID={$row['id_news']}, Title='{$row['title_news']}'<br>";
    } else {
        echo "❌ Not found in news table<br>";
        
        // Check if it's in posts table instead
        $query2 = "SELECT id_post, title_post, url_post FROM posts WHERE url_slug = ?";
        $stmt2 = $connection->prepare($query2);
        $stmt2->bind_param("s", $url);
        $stmt2->execute();
        $result2 = $stmt2->get_result();
        
        if ($result2 && $result2->num_rows > 0) {
            $row2 = $result2->fetch_assoc();
            echo "🔄 Found in POSTS table: ID={$row2['id_post']}, Title='{$row2['title_post']}'<br>";
        } else {
            echo "❌ Not found in posts table either<br>";
        }
    }
}

// Show all news entries to see what URLs exist
echo "<h3>All News URLs in Database</h3>";
$allNews = $connection->query("SELECT id_news, title_news, url_news FROM news ORDER BY id_news DESC LIMIT 10");
if ($allNews && $allNews->num_rows > 0) {
    echo "<table border='1'><tr><th>ID</th><th>Title</th><th>URL</th></tr>";
    while ($row = $allNews->fetch_assoc()) {
        echo "<tr><td>{$row['id_news']}</td><td>{$row['title_news']}</td><td>{$row['url_news']}</td></tr>";
    }
    echo "</table><br>";
} else {
    echo "❌ No news found in database<br>";
}

// Test GET parameters
echo "<h3>Current GET Parameters</h3>";
echo "<pre>";
print_r($_GET);
echo "</pre>";

$connection->close();
?>