<?php
// Debug comments loading issue
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Debug Comments Loading</h1>";

try {
    // Test database connection
    echo "<h2>🔧 Database Connection Test</h2>";
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
    
    if (isset($connection) && $connection) {
        echo "<p>✅ Database connection available</p>";
        
        // Test connection
        if ($connection->ping()) {
            echo "<p>✅ Database connection is active</p>";
        } else {
            echo "<p>❌ Database connection is not active</p>";
        }
    } else {
        echo "<p>❌ Database connection not available</p>";
        exit;
    }
    
    // Check comments table structure
    echo "<h2>📋 Comments Table Structure</h2>";
    $table_info = $connection->query("DESCRIBE comments");
    if ($table_info) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        while ($field = $table_info->fetch_assoc()) {
            echo "<tr>";
            foreach ($field as $value) {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Test post lookup
    echo "<h2>🔍 Post Lookup Test</h2>";
    $test_slug = 'kuda-dvigatsya-posle-shkoly';
    echo "<p>Testing post slug: <strong>{$test_slug}</strong></p>";
    
    // Test getEntityIdFromURL function
    echo "<h3>Testing getEntityIdFromURL function:</h3>";
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/includes/getEntityIdFromURL.php')) {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/getEntityIdFromURL.php';
        echo "<p>✅ getEntityIdFromURL.php loaded</p>";
        
        if (function_exists('getEntityIdFromURL')) {
            $post_id = getEntityIdFromURL('post', $test_slug);
            echo "<p>Post ID from function: <strong>{$post_id}</strong></p>";
        } else {
            echo "<p>❌ getEntityIdFromURL function not found</p>";
        }
    } else {
        echo "<p>❌ getEntityIdFromURL.php file not found</p>";
    }
    
    // Direct database query test
    echo "<h3>Direct Database Query Test:</h3>";
    $direct_query = "SELECT id, title_post FROM posts WHERE url_slug = '{$test_slug}' LIMIT 1";
    echo "<p>Query: <code>{$direct_query}</code></p>";
    
    $result = $connection->query($direct_query);
    if ($result && $result->num_rows > 0) {
        $post = $result->fetch_assoc();
        echo "<p>✅ Post found: ID {$post['id']}, Title: " . htmlspecialchars($post['title_post']) . "</p>";
        
        // Test comments for this post
        echo "<h3>Comments Test:</h3>";
        $comments_query = "SELECT COUNT(*) as count FROM comments WHERE entity_type = 'post' AND entity_id = {$post['id']}";
        $comments_result = $connection->query($comments_query);
        if ($comments_result) {
            $comment_count = $comments_result->fetch_assoc()['count'];
            echo "<p>Comments for this post (using entity_id): <strong>{$comment_count}</strong></p>";
        }
        
        // Also test with old column name to see if data exists
        $old_query = "SELECT COUNT(*) as count FROM comments WHERE entity_type = 'post' AND id_entity = {$post['id']}";
        $old_result = $connection->query($old_query);
        if ($old_result) {
            $old_count = $old_result->fetch_assoc()['count'];
            echo "<p>Comments for this post (using id_entity): <strong>{$old_count}</strong></p>";
        }
        
        // Show all comments for debugging
        echo "<h3>All Comments in Database:</h3>";
        $all_comments = $connection->query("SELECT id, entity_id, id_entity, entity_type, comment_text, date FROM comments ORDER BY id DESC LIMIT 10");
        if ($all_comments && $all_comments->num_rows > 0) {
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>ID</th><th>entity_id</th><th>id_entity</th><th>entity_type</th><th>comment_text</th><th>date</th></tr>";
            while ($comment = $all_comments->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $comment['id'] . "</td>";
                echo "<td>" . ($comment['entity_id'] ?? 'NULL') . "</td>";
                echo "<td>" . ($comment['id_entity'] ?? 'NULL') . "</td>";
                echo "<td>" . htmlspecialchars($comment['entity_type']) . "</td>";
                echo "<td>" . htmlspecialchars(substr($comment['comment_text'], 0, 50)) . "...</td>";
                echo "<td>" . $comment['date'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No comments found in database.</p>";
        }
        
        // Test loading comments component directly
        echo "<h2>🧪 Comments Component Test</h2>";
        echo "<p>Attempting to load comments component...</p>";
        
        // Set up variables that the component expects
        $currentUrl = "/post/{$test_slug}";
        $entityType = 'post';
        $entityId = $post['id'];
        
        echo "<p>Entity Type: {$entityType}</p>";
        echo "<p>Entity ID: {$entityId}</p>";
        echo "<p>Current URL: {$currentUrl}</p>";
        
        // Try to include the comments component
        ob_start();
        try {
            include $_SERVER['DOCUMENT_ROOT'] . '/comments/modern-comments-component.php';
            $comments_output = ob_get_contents();
            echo "<p>✅ Comments component loaded successfully</p>";
            echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
            echo "<h4>Comments Component Output:</h4>";
            echo $comments_output;
            echo "</div>";
        } catch (Exception $e) {
            ob_end_clean();
            echo "<p>❌ Error loading comments component: " . $e->getMessage() . "</p>";
        }
        ob_end_clean();
        
    } else {
        echo "<p>❌ Post not found with slug '{$test_slug}'</p>";
        
        // Show available posts
        $available = $connection->query("SELECT id, title_post, url_slug FROM posts WHERE url_slug IS NOT NULL ORDER BY id DESC LIMIT 5");
        echo "<p>Available posts:</p><ul>";
        while ($row = $available->fetch_assoc()) {
            echo "<li>ID {$row['id']}: " . htmlspecialchars($row['title_post']) . " (slug: {$row['url_slug']})</li>";
        }
        echo "</ul>";
    }
    
    // Test comment form processing
    echo "<h2>📝 Comment Form Test</h2>";
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/comments/process_comments.php')) {
        echo "<p>✅ process_comments.php exists</p>";
    } else {
        echo "<p>❌ process_comments.php not found</p>";
    }
    
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/comments/comment_form.php')) {
        echo "<p>✅ comment_form.php exists</p>";
    } else {
        echo "<p>❌ comment_form.php not found</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . " Line: " . $e->getLine() . "</p>";
}
?>