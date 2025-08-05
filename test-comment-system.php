<?php
// Simple test of comment system after URL field fixes
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Test Comment System</h1>";

try {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
    
    if (!isset($connection) || !$connection) {
        echo "<p>❌ Database connection not available</p>";
        exit;
    }
    
    echo "<p>✅ Database connected</p>";
    
    // Check if URL fields are now standardized
    echo "<h2>📊 URL Field Status</h2>";
    
    $tables = ['posts', 'news'];
    foreach ($tables as $table) {
        $result = $connection->query("SHOW COLUMNS FROM {$table} LIKE 'url_slug'");
        echo "<p>{$table}.url_slug: " . ($result->num_rows > 0 ? "✅ Exists" : "❌ Missing") . "</p>";
    }
    
    // Test finding the specific post
    echo "<h2>🔍 Post Lookup Test</h2>";
    $test_slug = 'kogda-ege-ostalis-pozadi';
    
    $post_query = "SELECT id, title, url_slug FROM posts WHERE url_slug = '{$test_slug}' LIMIT 1";
    $post_result = $connection->query($post_query);
    
    if ($post_result && $post_result->num_rows > 0) {
        $post = $post_result->fetch_assoc();
        echo "<p>✅ Found post: ID {$post['id']}</p>";
        echo "<p>Title: " . htmlspecialchars($post['title']) . "</p>";
        
        // Count comments for this post
        $comment_query = "SELECT COUNT(*) as total FROM comments WHERE entity_type = 'post' AND id_entity = {$post['id']}";
        $comment_result = $connection->query($comment_query);
        $comment_count = $comment_result->fetch_assoc()['total'];
        
        echo "<p>✅ Post has {$comment_count} comments</p>";
        
        if ($comment_count > 0) {
            echo "<p>✅ Comments exist - system should work now</p>";
        }
        
    } else {
        echo "<p>❌ Post not found with slug '{$test_slug}'</p>";
        
        // Show some available posts
        $available = $connection->query("SELECT id, title, url_slug FROM posts WHERE url_slug IS NOT NULL ORDER BY id DESC LIMIT 3");
        echo "<p>Available posts:</p><ul>";
        while ($row = $available->fetch_assoc()) {
            echo "<li><a href='/post/{$row['url_slug']}'>{$row['title']}</a></li>";
        }
        echo "</ul>";
    }
    
    echo "<h2>🎉 Test Complete</h2>";
    echo "<p>The URL fields have been standardized. Try the comment system now:</p>";
    echo "<p><a href='/post/kogda-ege-ostalis-pozadi' target='_blank'>Test Comment Submission</a></p>";
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}
?>