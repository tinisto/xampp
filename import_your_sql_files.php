<?php
/**
 * Import YOUR actual SQL files from Desktop
 */

echo "<h1>Importing Your Actual Database Content</h1>";

try {
    // Connect to SQLite
    $db = new PDO('sqlite:' . __DIR__ . '/database/local.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Clear test data first
    echo "<h2>Step 1: Clearing test data...</h2>";
    $db->exec("DELETE FROM news");
    $db->exec("DELETE FROM posts");
    echo "<p>✅ Cleared test data</p>";
    
    // Import news
    echo "<h2>Step 2: Importing YOUR news...</h2>";
    $newsFile = '/Users/anatolys/Desktop/SQL copy/news.sql';
    if (file_exists($newsFile)) {
        $sql = file_get_contents($newsFile);
        
        // Extract INSERT statements for news
        preg_match_all('/INSERT INTO `?news`?\s+(?:VALUES\s*)?\((.*?)\);/is', $sql, $matches);
        
        $newsCount = 0;
        foreach ($matches[0] as $insert) {
            try {
                // Convert MySQL to SQLite format
                $insert = str_replace('`', '', $insert);
                $insert = preg_replace('/\\\\\'/', "''", $insert);
                
                $db->exec($insert);
                $newsCount++;
            } catch (Exception $e) {
                // Continue on error
            }
        }
        echo "<p>✅ Imported $newsCount news articles</p>";
    }
    
    // Import posts
    echo "<h2>Step 3: Importing YOUR posts...</h2>";
    $postsFile = '/Users/anatolys/Desktop/SQL copy/posts.sql';
    if (file_exists($postsFile)) {
        $sql = file_get_contents($postsFile);
        
        // Extract INSERT statements for posts
        preg_match_all('/INSERT INTO `?posts`?\s+(?:VALUES\s*)?\((.*?)\);/is', $sql, $matches);
        
        $postsCount = 0;
        foreach ($matches[0] as $insert) {
            try {
                // Convert MySQL to SQLite format
                $insert = str_replace('`', '', $insert);
                $insert = preg_replace('/\\\\\'/', "''", $insert);
                
                $db->exec($insert);
                $postsCount++;
            } catch (Exception $e) {
                // Continue on error
            }
        }
        echo "<p>✅ Imported $postsCount posts</p>";
    }
    
    // Show sample of imported content
    echo "<h2>Your Content (Preview):</h2>";
    
    echo "<h3>Latest News:</h3>";
    $news = $db->query("SELECT id_news, title_news FROM news ORDER BY id_news DESC LIMIT 5")->fetchAll();
    echo "<ul>";
    foreach ($news as $item) {
        echo "<li>ID: {$item['id_news']} - " . htmlspecialchars($item['title_news']) . "</li>";
    }
    echo "</ul>";
    
    echo "<h3>Latest Posts:</h3>";
    $posts = $db->query("SELECT id, title_post FROM posts ORDER BY id DESC LIMIT 5")->fetchAll();
    echo "<ul>";
    foreach ($posts as $item) {
        echo "<li>ID: {$item['id']} - " . htmlspecialchars($item['title_post']) . "</li>";
    }
    echo "</ul>";
    
    echo '<p><a href="/" style="background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">🏠 View Your Site with YOUR Content</a></p>';
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}