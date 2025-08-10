<?php
require_once __DIR__ . '/database/db_modern.php';

echo "<h2>Testing PDO Query</h2>";

// Simple test without parameters
$posts1 = db_fetch_all("
    SELECT p.*, c.category_name, c.url_slug as category_slug
    FROM posts p
    LEFT JOIN categories c ON p.category = c.id_category
    WHERE 1=1
    ORDER BY p.date_post DESC
    LIMIT 12
");

echo "<p>Without parameters: Found " . count($posts1) . " posts</p>";
if (!empty($posts1)) {
    echo "<pre>First post: " . $posts1[0]['title_post'] . "</pre>";
}

// Test with LIMIT as parameter
$posts2 = db_fetch_all("
    SELECT p.*, c.category_name, c.url_slug as category_slug
    FROM posts p
    LEFT JOIN categories c ON p.category = c.id_category
    WHERE 1=1
    ORDER BY p.date_post DESC
    LIMIT ? OFFSET ?
", [12, 0]);

echo "<p>With parameters: Found " . count($posts2) . " posts</p>";
if (!empty($posts2)) {
    echo "<pre>First post: " . $posts2[0]['title_post'] . "</pre>";
}

// Test the exact query from posts_modern.php
$whereClause = "1=1";
$params = [];
$perPage = 12;
$offset = 0;
$orderBy = "p.date_post DESC";

$posts3 = db_fetch_all("
    SELECT p.*, c.category_name, c.url_slug as category_slug
    FROM posts p
    LEFT JOIN categories c ON p.category = c.id_category
    WHERE $whereClause
    ORDER BY $orderBy
    LIMIT ? OFFSET ?
", array_merge($params, [$perPage, $offset]));

echo "<p>Exact query from posts_modern.php: Found " . count($posts3) . " posts</p>";
if (!empty($posts3)) {
    echo "<pre>First post: " . $posts3[0]['title_post'] . "</pre>";
}
?>