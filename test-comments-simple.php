<?php
require_once 'database/db_connections.php';

echo "<h1>Simple Comments Test</h1>";

// Test 1: Check all comments
echo "<h2>All Comments in Database:</h2>";
$query = "SELECT id, entity_id, entity_type, comment_text, date FROM comments ORDER BY date DESC LIMIT 10";
$result = mysqli_query($connection, $query);

if ($result) {
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>entity_id</th><th>entity_type</th><th>comment_text</th><th>date</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['entity_id']}</td>";
        echo "<td>{$row['entity_type']}</td>";
        echo "<td>" . substr($row['comment_text'], 0, 50) . "...</td>";
        echo "<td>{$row['date']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>Error: " . mysqli_error($connection) . "</p>";
}

// Test 2: Check specific post
echo "<h2>Post 'kogda-ege-ostalis-pozadi' Details:</h2>";
$query = "SELECT id, title_post, url_slug FROM posts WHERE url_slug = 'kogda-ege-ostalis-pozadi'";
$result = mysqli_query($connection, $query);

if ($result && $row = mysqli_fetch_assoc($result)) {
    echo "<p>Post ID: {$row['id']}</p>";
    echo "<p>Title: {$row['title_post']}</p>";
    echo "<p>URL Slug: {$row['url_slug']}</p>";
    
    $post_id = $row['id'];
    
    // Test 3: Check comments for this specific post
    echo "<h2>Comments for this post (ID: $post_id):</h2>";
    $query = "SELECT * FROM comments WHERE entity_id = $post_id AND entity_type = 'post'";
    $result = mysqli_query($connection, $query);
    
    if ($result) {
        $count = mysqli_num_rows($result);
        echo "<p>Found $count comments</p>";
        
        if ($count > 0) {
            while ($comment = mysqli_fetch_assoc($result)) {
                echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
                echo "<p><strong>Comment ID:</strong> {$comment['id']}</p>";
                echo "<p><strong>Entity ID:</strong> {$comment['entity_id']}</p>";
                echo "<p><strong>Text:</strong> {$comment['comment_text']}</p>";
                echo "<p><strong>Date:</strong> {$comment['date']}</p>";
                echo "</div>";
            }
        }
    } else {
        echo "<p>Error: " . mysqli_error($connection) . "</p>";
    }
} else {
    echo "<p>Post not found!</p>";
}

// Test 4: Direct test of what comment form would save
echo "<h2>What getEntityIdFromURL returns for this post:</h2>";
require_once 'includes/functions/getEntityIdFromURL.php';
$_SERVER['REQUEST_URI'] = '/post/kogda-ege-ostalis-pozadi';
$result = getEntityIdFromURL($connection, 'post');
echo "<pre>";
var_dump($result);
echo "</pre>";
?>