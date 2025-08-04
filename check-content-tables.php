<?php
// Check news and posts table structure
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/check_admin.php';

// Include database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/config/environment.php';
if (!isset($connection)) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
}

header('Content-Type: text/plain; charset=utf-8');

echo "=== Checking Content Tables Structure ===\n\n";

// Check news table
echo "1. NEWS TABLE:\n";
$news_result = $connection->query("SHOW COLUMNS FROM news");
if ($news_result) {
    echo "Columns in news table:\n";
    while ($row = $news_result->fetch_assoc()) {
        echo "  - " . $row['Field'] . " (" . $row['Type'] . ")" . ($row['Null'] === 'NO' ? ' NOT NULL' : '') . "\n";
    }
} else {
    echo "ERROR: news table not found or error: " . $connection->error . "\n";
}

echo "\n2. POSTS TABLE:\n";
$posts_result = $connection->query("SHOW COLUMNS FROM posts");
if ($posts_result) {
    echo "Columns in posts table:\n";
    while ($row = $posts_result->fetch_assoc()) {
        echo "  - " . $row['Field'] . " (" . $row['Type'] . ")" . ($row['Null'] === 'NO' ? ' NOT NULL' : '') . "\n";
    }
} else {
    echo "ERROR: posts table not found or error: " . $connection->error . "\n";
}

// Show sample insert queries
echo "\n3. SAMPLE INSERT QUERIES:\n";
echo "News: INSERT INTO news (title, content, author, created_at) VALUES (?, ?, ?, NOW())\n";
echo "Posts: INSERT INTO posts (title, content, user_id, created_at) VALUES (?, ?, ?, NOW())\n";

$connection->close();
?>