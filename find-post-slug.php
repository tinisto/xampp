<?php
// Find the correct post slug
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Find Post Slug</h1>";

try {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
    
    if (!isset($connection) || !$connection) {
        echo "<p>❌ Database connection not available</p>";
        exit;
    }
    
    echo "<p>✅ Database connected</p>";
    
    // Search for posts with 'ege' in title or slug
    echo "<h2>🔍 Posts containing 'ege':</h2>";
    $search_query = "SELECT id, title, url_slug FROM posts WHERE (title LIKE '%ege%' OR title LIKE '%ЕГЭ%' OR url_slug LIKE '%ege%') ORDER BY id DESC LIMIT 10";
    $result = $connection->query($search_query);
    
    if ($result && $result->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Title</th><th>URL Slug</th><th>Link</th></tr>";
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>" . htmlspecialchars($row['title']) . "</td>";
            echo "<td>{$row['url_slug']}</td>";
            echo "<td><a href='/post/{$row['url_slug']}' target='_blank'>Test</a></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No posts found with 'ege' in title</p>";
    }
    
    // Show recent posts with slugs
    echo "<h2>📋 Recent posts with slugs:</h2>";
    $recent_query = "SELECT id, title, url_slug FROM posts WHERE url_slug IS NOT NULL AND url_slug != '' ORDER BY id DESC LIMIT 20";
    $recent_result = $connection->query($recent_query);
    
    if ($recent_result && $recent_result->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Title</th><th>URL Slug</th><th>Link</th></tr>";
        
        while ($row = $recent_result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>" . htmlspecialchars(substr($row['title'], 0, 50)) . "...</td>";
            echo "<td>{$row['url_slug']}</td>";
            echo "<td><a href='/post/{$row['url_slug']}' target='_blank'>Test</a></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No posts found with slugs</p>";
    }
    
    // Check if posts have comments
    echo "<h2>💬 Posts with comments:</h2>";
    $comments_query = "SELECT p.id, p.title, p.url_slug, COUNT(c.id) as comment_count 
                      FROM posts p 
                      LEFT JOIN comments c ON c.entity_type = 'post' AND c.id_entity = p.id 
                      WHERE p.url_slug IS NOT NULL 
                      GROUP BY p.id 
                      HAVING comment_count > 0 
                      ORDER BY comment_count DESC 
                      LIMIT 10";
    
    $comments_result = $connection->query($comments_query);
    
    if ($comments_result && $comments_result->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Title</th><th>URL Slug</th><th>Comments</th><th>Test Link</th></tr>";
        
        while ($row = $comments_result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>" . htmlspecialchars(substr($row['title'], 0, 50)) . "...</td>";
            echo "<td>{$row['url_slug']}</td>";
            echo "<td>{$row['comment_count']}</td>";
            echo "<td><a href='/post/{$row['url_slug']}' target='_blank'>Test Comments</a></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No posts found with comments</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}
?>