<?php
require_once __DIR__ . '/config/local-config.php';

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$mysqli->set_charset(DB_CHARSET);

echo "<h2>Debugging Posts Query</h2>";

// Test the exact query from posts_modern.php
$where = "1=1"; // No is_published check
$query = "
    SELECT p.*, c.category_name, c.url_slug as category_slug
    FROM posts p
    LEFT JOIN categories c ON p.category = c.id_category
    WHERE $where
    ORDER BY p.date_post DESC
    LIMIT 12 OFFSET 0
";

echo "<h3>Query:</h3>";
echo "<pre>" . htmlspecialchars($query) . "</pre>";

$result = $mysqli->query($query);

if ($result) {
    echo "<p>Query successful! Found " . $result->num_rows . " posts</p>";
    
    echo "<h3>First 3 posts:</h3>";
    $count = 0;
    while ($count < 3 && $row = $result->fetch_assoc()) {
        echo "<pre>";
        echo "ID: " . $row['id'] . "\n";
        echo "Title: " . $row['title_post'] . "\n";
        echo "Date: " . $row['date_post'] . "\n";
        echo "Category ID: " . $row['category'] . "\n";
        echo "Category Name: " . ($row['category_name'] ?? 'NULL') . "\n";
        echo "URL Slug: " . $row['url_slug'] . "\n";
        echo "</pre>";
        $count++;
    }
} else {
    echo "<p>Query failed: " . $mysqli->error . "</p>";
}

// Check total posts count
$total = $mysqli->query("SELECT COUNT(*) as count FROM posts")->fetch_assoc()['count'];
echo "<p>Total posts in database: $total</p>";

$mysqli->close();
?>