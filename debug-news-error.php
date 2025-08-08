<?php
// Debug news-single.php errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Testing News Single Page Logic</h2>";

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Test with a fake URL to see what happens
$newsUrl = 'sdfdsfd';
echo "<p>Testing with URL: <strong>$newsUrl</strong></p>";

try {
    echo "<h3>1. Database connection test:</h3>";
    if ($connection) {
        echo "✅ Database connected<br>";
    } else {
        echo "❌ Database connection failed<br>";
        exit;
    }
    
    echo "<h3>2. Testing query:</h3>";
    $query = "SELECT n.*, c.title_category, c.url_category, 
                     u.username as author_name,
                     (SELECT COUNT(*) FROM comments WHERE entity_type = 'news' AND entity_id = n.id) as comment_count
              FROM news n
              LEFT JOIN categories c ON n.category_news = c.id_category
              LEFT JOIN users u ON n.user_id = u.id
              WHERE n.url_slug = ? AND n.approved = 1";
    
    echo "<p>Query: " . htmlspecialchars($query) . "</p>";
    
    $stmt = $connection->prepare($query);
    if (!$stmt) {
        echo "❌ Prepare failed: " . $connection->error . "<br>";
        exit;
    }
    
    $stmt->bind_param("s", $newsUrl);
    if (!$stmt->execute()) {
        echo "❌ Execute failed: " . $stmt->error . "<br>";
        exit;
    }
    
    $result = $stmt->get_result();
    if (!$result) {
        echo "❌ Get result failed: " . $stmt->error . "<br>";
        exit;
    }
    
    echo "✅ Query executed successfully<br>";
    echo "Rows found: " . $result->num_rows . "<br>";
    
    $news = $result->fetch_assoc();
    if (!$news) {
        echo "✅ No news found (expected for fake URL)<br>";
        echo "This should redirect to /404<br>";
    } else {
        echo "✅ News found: " . htmlspecialchars($news['title_news']) . "<br>";
    }
    
    echo "<h3>3. Testing with real URL:</h3>";
    // Try to find an actual URL
    $realUrlQuery = "SELECT url_slug FROM news WHERE approved = 1 LIMIT 1";
    $realResult = mysqli_query($connection, $realUrlQuery);
    if ($realResult && mysqli_num_rows($realResult) > 0) {
        $realRow = mysqli_fetch_assoc($realResult);
        $realUrl = $realRow['url_slug'];
        echo "Found real URL: <a href='/news/$realUrl' target='_blank'>$realUrl</a><br>";
        
        // Test the query with real URL
        $stmt2 = $connection->prepare($query);
        $stmt2->bind_param("s", $realUrl);
        $stmt2->execute();
        $result2 = $stmt2->get_result();
        echo "Real URL query rows: " . $result2->num_rows . "<br>";
    } else {
        echo "No approved news found<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "<br>";
    echo "❌ Stack trace: <pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<h3>4. Check if comments file exists:</h3>";
$commentsFile = $_SERVER['DOCUMENT_ROOT'] . '/comments/display_comments.php';
if (file_exists($commentsFile)) {
    echo "✅ Comments file exists: $commentsFile<br>";
} else {
    echo "❌ Comments file missing: $commentsFile<br>";
}

mysqli_close($connection);
?>