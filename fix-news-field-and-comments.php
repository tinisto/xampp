<?php
// Fix news table field and comment system
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Fix News Field and Comment System</h1>";

try {
    $connection = new mysqli('localhost', 'franko', 'JyvR!HK2E!N55Zt', '11klassniki_claude');
    
    if ($connection->connect_error) {
        throw new Exception("Connection failed: " . $connection->connect_error);
    }
    
    $connection->set_charset("utf8mb4");
    echo "<p>✅ Database connected</p>";
    
    echo "<h2>🔧 Fixing News Table Field</h2>";
    
    // Check current news table structure
    $result = $connection->query("SHOW COLUMNS FROM news LIKE 'url_%'");
    echo "<p>Current URL fields in news table:</p><ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li>{$row['Field']} - {$row['Type']}</li>";
    }
    echo "</ul>";
    
    // Fix the news field without key constraint issues
    $check_news = $connection->query("SHOW COLUMNS FROM news LIKE 'url_news'");
    if ($check_news->num_rows > 0) {
        echo "<p>Attempting to rename url_news to url_slug...</p>";
        
        // First, drop any indexes on url_news
        $connection->query("ALTER TABLE news DROP INDEX IF EXISTS url_news");
        $connection->query("ALTER TABLE news DROP INDEX IF EXISTS idx_url_news");
        
        // Then rename the column with a shorter length to avoid key issues
        $rename_sql = "ALTER TABLE news CHANGE COLUMN url_news url_slug VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        
        if ($connection->query($rename_sql)) {
            echo "<p>✅ Successfully renamed url_news to url_slug in news table</p>";
        } else {
            echo "<p>❌ Failed to rename news field: " . $connection->error . "</p>";
            
            // Alternative approach: Add new column and copy data
            echo "<p>Trying alternative approach...</p>";
            
            $add_col = "ALTER TABLE news ADD COLUMN url_slug VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
            if ($connection->query($add_col)) {
                echo "<p>✅ Added url_slug column</p>";
                
                $copy_data = "UPDATE news SET url_slug = url_news WHERE url_news IS NOT NULL";
                if ($connection->query($copy_data)) {
                    echo "<p>✅ Copied data from url_news to url_slug</p>";
                    
                    $drop_old = "ALTER TABLE news DROP COLUMN url_news";
                    if ($connection->query($drop_old)) {
                        echo "<p>✅ Dropped old url_news column</p>";
                    }
                }
            }
        }
    } else {
        echo "<p>✅ url_news field already renamed or doesn't exist</p>";
    }
    
    echo "<h2>🔧 Testing Comment System</h2>";
    
    // Test if we can find the post
    $test_post_sql = "SELECT id, title, url_slug FROM posts WHERE url_slug = 'kogda-ege-ostalis-pozadi' LIMIT 1";
    $post_result = $connection->query($test_post_sql);
    
    if ($post_result && $post_result->num_rows > 0) {
        $post = $post_result->fetch_assoc();
        echo "<p>✅ Found post: ID {$post['id']}, Title: {$post['title']}</p>";
        
        // Test comments for this post
        $comments_sql = "SELECT COUNT(*) as comment_count FROM comments WHERE entity_type = 'post' AND id_entity = ?";
        $stmt = $connection->prepare($comments_sql);
        $stmt->bind_param("i", $post['id']);
        $stmt->execute();
        $comment_result = $stmt->get_result();
        $comment_count = $comment_result->fetch_assoc()['comment_count'];
        
        echo "<p>✅ Post has {$comment_count} comments</p>";
        
    } else {
        echo "<p>❌ Could not find post with slug 'kogda-ege-ostalis-pozadi'</p>";
        
        // Show available posts
        $all_posts = $connection->query("SELECT id, title, url_slug FROM posts ORDER BY id DESC LIMIT 5");
        echo "<p>Recent posts:</p><ul>";
        while ($row = $all_posts->fetch_assoc()) {
            echo "<li>ID {$row['id']}: {$row['title']} (slug: {$row['url_slug']})</li>";
        }
        echo "</ul>";
    }
    
    echo "<h2>📊 Final Status</h2>";
    
    // Check final field status
    $posts_check = $connection->query("SHOW COLUMNS FROM posts LIKE 'url_slug'");
    $news_check = $connection->query("SHOW COLUMNS FROM news LIKE 'url_slug'");
    
    echo "<p>Posts table url_slug: " . ($posts_check->num_rows > 0 ? "✅ Exists" : "❌ Missing") . "</p>";
    echo "<p>News table url_slug: " . ($news_check->num_rows > 0 ? "✅ Exists" : "❌ Missing") . "</p>";
    
    $connection->close();
    
    echo "<h2>🎉 Fix Complete</h2>";
    echo "<p>Now test the comment system at: <a href='/post/kogda-ege-ostalis-pozadi' target='_blank'>Post Link</a></p>";
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}
?>