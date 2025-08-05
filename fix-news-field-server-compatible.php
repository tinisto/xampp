<?php
// Fix news field using server's existing connection method
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Fix News Field - Server Compatible</h1>";

// Use the site's existing connection method
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

if (!isset($connection) || !$connection) {
    echo "<p>❌ Database connection not available</p>";
    exit;
}

echo "<p>✅ Database connected using site's connection</p>";

try {
    echo "<h2>🔧 Fixing News Table Field</h2>";
    
    // Check current news table structure
    $result = $connection->query("SHOW COLUMNS FROM news LIKE 'url_%'");
    echo "<p>Current URL fields in news table:</p><ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li>{$row['Field']} - {$row['Type']}</li>";
    }
    echo "</ul>";
    
    // Check if url_news exists and url_slug doesn't
    $check_old = $connection->query("SHOW COLUMNS FROM news LIKE 'url_news'");
    $check_new = $connection->query("SHOW COLUMNS FROM news LIKE 'url_slug'");
    
    if ($check_old->num_rows > 0 && $check_new->num_rows === 0) {
        echo "<p>📋 Need to rename url_news to url_slug</p>";
        
        // Method 1: Simple rename (might fail due to key length)
        $rename_sql = "ALTER TABLE news CHANGE COLUMN url_news url_slug VARCHAR(191)";
        
        if ($connection->query($rename_sql)) {
            echo "<p>✅ Successfully renamed url_news to url_slug</p>";
        } else {
            echo "<p>⚠️ Simple rename failed: " . $connection->error . "</p>";
            echo "<p>Trying alternative method...</p>";
            
            // Method 2: Add new column, copy data, drop old
            $steps = [
                "ALTER TABLE news ADD COLUMN url_slug VARCHAR(191)" => "Added url_slug column",
                "UPDATE news SET url_slug = url_news WHERE url_news IS NOT NULL" => "Copied data",
                "ALTER TABLE news DROP COLUMN url_news" => "Dropped old column"
            ];
            
            foreach ($steps as $sql => $description) {
                if ($connection->query($sql)) {
                    echo "<p>✅ {$description}</p>";
                } else {
                    echo "<p>❌ Failed: {$description} - " . $connection->error . "</p>";
                    break;
                }
            }
        }
    } else if ($check_new->num_rows > 0) {
        echo "<p>✅ url_slug already exists in news table</p>";
    } else {
        echo "<p>⚠️ Unexpected state - please check manually</p>";
    }
    
    echo "<h2>🔧 Testing Comment System</h2>";
    
    // Test the specific post
    $test_slug = 'kogda-ege-ostalis-pozadi';
    $test_post_sql = "SELECT id, title, url_slug FROM posts WHERE url_slug = ? LIMIT 1";
    $stmt = $connection->prepare($test_post_sql);
    $stmt->bind_param("s", $test_slug);
    $stmt->execute();
    $post_result = $stmt->get_result();
    
    if ($post_result && $post_result->num_rows > 0) {
        $post = $post_result->fetch_assoc();
        echo "<p>✅ Found post: ID {$post['id']}, Title: " . htmlspecialchars($post['title']) . "</p>";
        
        // Check comments for this post
        $comments_sql = "SELECT COUNT(*) as comment_count FROM comments WHERE entity_type = 'post' AND id_entity = ?";
        $stmt2 = $connection->prepare($comments_sql);
        $stmt2->bind_param("i", $post['id']);
        $stmt2->execute();
        $comment_result = $stmt2->get_result();
        $comment_count = $comment_result->fetch_assoc()['comment_count'];
        
        echo "<p>✅ Post has {$comment_count} comments</p>";
        
        // Test if we can load comments
        echo "<p>Testing comment loading...</p>";
        $load_comments_sql = "SELECT id, comment_text FROM comments WHERE entity_type = 'post' AND id_entity = ? ORDER BY date DESC LIMIT 3";
        $stmt3 = $connection->prepare($load_comments_sql);
        $stmt3->bind_param("i", $post['id']);
        $stmt3->execute();
        $comments = $stmt3->get_result();
        
        if ($comments->num_rows > 0) {
            echo "<p>✅ Comments loaded successfully:</p><ul>";
            while ($comment = $comments->fetch_assoc()) {
                echo "<li>Comment {$comment['id']}: " . htmlspecialchars(substr($comment['comment_text'], 0, 50)) . "...</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>⚠️ No comments found for this post</p>";
        }
        
    } else {
        echo "<p>❌ Could not find post with slug '{$test_slug}'</p>";
        
        // Show available posts
        $all_posts = $connection->query("SELECT id, title, url_slug FROM posts WHERE url_slug IS NOT NULL ORDER BY id DESC LIMIT 5");
        echo "<p>Recent posts with slugs:</p><ul>";
        while ($row = $all_posts->fetch_assoc()) {
            echo "<li>ID {$row['id']}: " . htmlspecialchars($row['title']) . " (slug: {$row['url_slug']})</li>";
        }
        echo "</ul>";
    }
    
    echo "<h2>📊 Final Status Check</h2>";
    
    // Check all tables have url_slug
    $tables = ['posts', 'news'];
    foreach ($tables as $table) {
        $check = $connection->query("SHOW COLUMNS FROM {$table} LIKE 'url_slug'");
        echo "<p>{$table} table url_slug: " . ($check->num_rows > 0 ? "✅ Exists" : "❌ Missing") . "</p>";
    }
    
    echo "<h2>🎉 Fix Attempt Complete</h2>";
    echo "<p>Now test comments at: <a href='/post/kogda-ege-ostalis-pozadi' target='_blank'>/post/kogda-ege-ostalis-pozadi</a></p>";
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . " Line: " . $e->getLine() . "</p>";
}
?>