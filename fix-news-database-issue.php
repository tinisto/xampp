<?php
// Script to analyze and fix news database issue
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>News Database Analysis and Fix</h2>";

// Check if news table exists
$newsTableExists = $connection->query("SHOW TABLES LIKE 'news'")->num_rows > 0;
$postsTableExists = $connection->query("SHOW TABLES LIKE 'posts'")->num_rows > 0;

echo "<h3>Table Status:</h3>";
echo "<p>News table exists: " . ($newsTableExists ? "YES" : "NO") . "</p>";
echo "<p>Posts table exists: " . ($postsTableExists ? "YES" : "NO") . "</p>";

if ($newsTableExists) {
    $newsCount = $connection->query("SELECT COUNT(*) FROM news")->fetch_row()[0];
    echo "<p>Records in news table: $newsCount</p>";
}

if ($postsTableExists) {
    $postsCount = $connection->query("SELECT COUNT(*) FROM posts")->fetch_row()[0];
    echo "<p>Records in posts table: $postsCount</p>";
    
    // Check for news categories in posts table
    $newsCategories = ['novosti-vuzov', 'novosti-spo', 'novosti-shkol', 'novosti-obrazovaniya'];
    $categoryString = "'" . implode("','", $newsCategories) . "'";
    
    $newsInPosts = $connection->query("SELECT COUNT(*) FROM posts WHERE category_post IN ($categoryString)")->fetch_row()[0];
    echo "<p>News-related posts in posts table: $newsInPosts</p>";
    
    if ($newsInPosts > 0) {
        echo "<h3>Sample news from posts table:</h3>";
        $sampleQuery = "SELECT title_post, category_post, date_post FROM posts WHERE category_post IN ($categoryString) ORDER BY date_post DESC LIMIT 5";
        $result = $connection->query($sampleQuery);
        while ($row = $result->fetch_assoc()) {
            echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 5px;'>";
            echo "<strong>Title:</strong> " . htmlspecialchars($row['title_post']) . "<br>";
            echo "<strong>Category:</strong> " . htmlspecialchars($row['category_post']) . "<br>";
            echo "<strong>Date:</strong> " . $row['date_post'] . "<br>";
            echo "</div>";
        }
    }
}

// Create news table if it doesn't exist and populate from posts
if (!$newsTableExists && $postsTableExists && $newsInPosts > 0) {
    echo "<h3>Creating news table and migrating data...</h3>";
    
    // Create news table
    $createTableSQL = "CREATE TABLE IF NOT EXISTS `news` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `title_news` varchar(255) NOT NULL,
        `text_news` text,
        `url_slug` varchar(255) DEFAULT NULL,
        `image_news` varchar(255) DEFAULT NULL,
        `date_news` datetime DEFAULT NULL,
        `category_news` varchar(50) DEFAULT NULL,
        `author_id` int(11) DEFAULT NULL,
        `views` int(11) DEFAULT 0,
        `status` varchar(20) DEFAULT 'published',
        PRIMARY KEY (`id`),
        KEY `idx_category` (`category_news`),
        KEY `idx_date` (`date_news`),
        KEY `idx_slug` (`url_slug`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if ($connection->query($createTableSQL)) {
        echo "<p style='color: green;'>✓ News table created successfully</p>";
        
        // Map category_post values to category_news
        $categoryMap = [
            'novosti-vuzov' => '1',
            'novosti-spo' => '2',
            'novosti-shkol' => '3',
            'novosti-obrazovaniya' => '4'
        ];
        
        // Migrate data from posts to news
        $migrateCount = 0;
        foreach ($categoryMap as $postCat => $newsCat) {
            $migrateSQL = "INSERT INTO news (title_news, text_news, url_slug, image_news, date_news, category_news, author_id)
                          SELECT title_post, text_post, url_slug, 
                                 CASE 
                                    WHEN image1 IS NOT NULL AND image1 != '' THEN image1
                                    WHEN image2 IS NOT NULL AND image2 != '' THEN image2
                                    WHEN image3 IS NOT NULL AND image3 != '' THEN image3
                                    ELSE NULL
                                 END as image_news,
                                 date_post, '$newsCat', author
                          FROM posts 
                          WHERE category_post = '$postCat'";
            
            if ($connection->query($migrateSQL)) {
                $affected = $connection->affected_rows;
                $migrateCount += $affected;
                echo "<p>Migrated $affected posts from category '$postCat' to news category '$newsCat'</p>";
            } else {
                echo "<p style='color: red;'>Error migrating $postCat: " . $connection->error . "</p>";
            }
        }
        
        echo "<p style='color: green;'>✓ Total migrated: $migrateCount news items</p>";
    } else {
        echo "<p style='color: red;'>Error creating news table: " . $connection->error . "</p>";
    }
}

// If news table exists but is empty, check if we should populate it
if ($newsTableExists && $newsCount == 0 && $postsTableExists) {
    echo "<h3>News table is empty. Checking for news data in posts table...</h3>";
    
    $newsCategories = ['novosti-vuzov', 'novosti-spo', 'novosti-shkol', 'novosti-obrazovaniya'];
    $categoryString = "'" . implode("','", $newsCategories) . "'";
    $newsInPosts = $connection->query("SELECT COUNT(*) FROM posts WHERE category_post IN ($categoryString)")->fetch_row()[0];
    
    if ($newsInPosts > 0) {
        echo "<p>Found $newsInPosts news items in posts table</p>";
        echo "<p><a href='?migrate=1' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Migrate News Data</a></p>";
        
        if (isset($_GET['migrate'])) {
            // Perform migration
            $categoryMap = [
                'novosti-vuzov' => '1',
                'novosti-spo' => '2',
                'novosti-shkol' => '3',
                'novosti-obrazovaniya' => '4'
            ];
            
            $migrateCount = 0;
            foreach ($categoryMap as $postCat => $newsCat) {
                $migrateSQL = "INSERT INTO news (title_news, text_news, url_slug, image_news, date_news, category_news, author_id)
                              SELECT title_post, text_post, url_slug, 
                                     CASE 
                                        WHEN image1 IS NOT NULL AND image1 != '' THEN image1
                                        WHEN image2 IS NOT NULL AND image2 != '' THEN image2
                                        WHEN image3 IS NOT NULL AND image3 != '' THEN image3
                                        ELSE NULL
                                     END as image_news,
                                     date_post, '$newsCat', author
                              FROM posts 
                              WHERE category_post = '$postCat'";
                
                if ($connection->query($migrateSQL)) {
                    $affected = $connection->affected_rows;
                    $migrateCount += $affected;
                    echo "<p style='color: green;'>✓ Migrated $affected posts from category '$postCat'</p>";
                }
            }
            
            echo "<p style='color: green;'><strong>✓ Migration complete! Total: $migrateCount news items</strong></p>";
        }
    }
}

// Final status
echo "<h3>Final Status:</h3>";
if ($newsTableExists) {
    $finalCount = $connection->query("SELECT COUNT(*) FROM news")->fetch_row()[0];
    echo "<p>News table has <strong>$finalCount</strong> records</p>";
    
    if ($finalCount > 0) {
        echo "<p style='color: green; font-size: 18px;'>✓ News system is ready!</p>";
        echo "<p><a href='/news' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>View News Page</a></p>";
    }
}
?>