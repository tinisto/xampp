<?php
require_once __DIR__ . '/config/local-config.php';

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$mysqli->set_charset(DB_CHARSET);

echo "<h2>Categories Table Structure</h2>";

// Check columns
$result = $mysqli->query("SHOW COLUMNS FROM categories");
echo "<pre>";
while ($row = $result->fetch_assoc()) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
echo "</pre>";

// Get sample data
echo "<h2>Sample Categories</h2>";
$result = $mysqli->query("SELECT * FROM categories LIMIT 10");
if ($result) {
    echo "<pre>";
    while ($row = $result->fetch_assoc()) {
        print_r($row);
    }
    echo "</pre>";
}

// Check if posts are using valid category IDs
echo "<h2>Category Usage in Posts</h2>";
$result = $mysqli->query("SELECT DISTINCT category FROM posts ORDER BY category");
echo "<p>Category IDs used in posts: ";
$cats = [];
while ($row = $result->fetch_assoc()) {
    $cats[] = $row['category'];
}
echo implode(', ', $cats) . "</p>";

$mysqli->close();
?>