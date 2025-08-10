<?php
require_once __DIR__ . '/config/local-config.php';

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$mysqli->set_charset(DB_CHARSET);

echo "<h2>Testing Posts Query</h2>";

// Check if is_published column exists
$columns = $mysqli->query("SHOW COLUMNS FROM posts");
$columnNames = [];
while ($col = $columns->fetch_assoc()) {
    $columnNames[] = $col['Field'];
}
echo "<p>Posts table columns: " . implode(', ', $columnNames) . "</p>";

// Get sample posts without is_published filter
echo "<h3>Sample Posts (no filter):</h3>";
$result = $mysqli->query("SELECT id, title_post, date_post, category FROM posts ORDER BY date_post DESC LIMIT 5");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "<p>ID: {$row['id']}, Title: {$row['title_post']}, Date: {$row['date_post']}, Category: {$row['category']}</p>";
    }
}

// Check categories table
echo "<h3>Categories:</h3>";
$result = $mysqli->query("SELECT * FROM categories LIMIT 10");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "<p>ID: {$row['id']}, Name: {$row['name']}</p>";
    }
}

$mysqli->close();
?>