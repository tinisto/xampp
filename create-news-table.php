<?php
// Create news table script
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Create News Table</h1>";

// Check if news table already exists
$tableExists = $connection->query("SHOW TABLES LIKE 'news'")->num_rows > 0;

if ($tableExists) {
    echo "<p style='color: orange;'>⚠️ News table already exists!</p>";
    
    // Show current structure
    $cols = $connection->query("SHOW COLUMNS FROM news");
    echo "<h3>Current news table structure:</h3>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($col = $cols->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$col['Field']}</td>";
        echo "<td>{$col['Type']}</td>";
        echo "<td>{$col['Null']}</td>";
        echo "<td>{$col['Key']}</td>";
        echo "<td>{$col['Default']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    $count = $connection->query("SELECT COUNT(*) FROM news")->fetch_row()[0];
    echo "<p>Current record count: <strong>$count</strong></p>";
    
} else {
    echo "<p>Creating news table...</p>";
    
    $createTableSQL = "CREATE TABLE `news` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `title_news` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        `text_news` text COLLATE utf8mb4_unicode_ci,
        `url_slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `image_news` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `date_news` datetime DEFAULT NULL,
        `category_news` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `author_id` int(11) DEFAULT NULL,
        `views` int(11) DEFAULT 0,
        `status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'published',
        `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `meta_description` text COLLATE utf8mb4_unicode_ci,
        `meta_keywords` text COLLATE utf8mb4_unicode_ci,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `idx_category` (`category_news`),
        KEY `idx_date` (`date_news`),
        KEY `idx_slug` (`url_slug`),
        KEY `idx_status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if ($connection->query($createTableSQL)) {
        echo "<p style='color: green;'>✓ News table created successfully!</p>";
        
        // Add some sample data
        echo "<h3>Adding sample news data...</h3>";
        
        $sampleData = [
            [
                'title' => 'Новые правила поступления в ВУЗы 2025',
                'text' => 'Министерство образования опубликовало обновленные правила приема в высшие учебные заведения на 2025 учебный год...',
                'category' => '1',
                'slug' => 'novye-pravila-postupleniya-v-vuzy-2025'
            ],
            [
                'title' => 'Топ-10 колледжей России по трудоустройству',
                'text' => 'Рейтинг средних профессиональных учебных заведений по показателям трудоустройства выпускников...',
                'category' => '2',
                'slug' => 'top-10-kolledzhej-rossii-po-trudoustrojstvu'
            ],
            [
                'title' => 'ЕГЭ 2025: изменения в экзаменах',
                'text' => 'ФИПИ представил изменения в контрольно-измерительных материалах ЕГЭ на 2025 год...',
                'category' => '3',
                'slug' => 'ege-2025-izmeneniya-v-ekzamenah'
            ],
            [
                'title' => 'Цифровизация образования: новые технологии',
                'text' => 'Обзор современных технологий, которые внедряются в образовательный процесс...',
                'category' => '4',
                'slug' => 'cifrovizaciya-obrazovaniya-novye-tekhnologii'
            ]
        ];
        
        $inserted = 0;
        foreach ($sampleData as $news) {
            $stmt = $connection->prepare("INSERT INTO news (title_news, text_news, url_slug, category_news, date_news) VALUES (?, ?, ?, ?, NOW())");
            $stmt->bind_param("ssss", $news['title'], $news['text'], $news['slug'], $news['category']);
            if ($stmt->execute()) {
                $inserted++;
            }
            $stmt->close();
        }
        
        echo "<p style='color: green;'>✓ Inserted $inserted sample news items</p>";
        
    } else {
        echo "<p style='color: red;'>✗ Error creating table: " . $connection->error . "</p>";
    }
}

// Check if we should populate from posts table
$postsWithNews = $connection->query("SELECT COUNT(*) FROM posts WHERE category_post IN ('novosti-vuzov', 'novosti-spo', 'novosti-shkol', 'novosti-obrazovaniya')")->fetch_row()[0] ?? 0;

if ($postsWithNews > 0) {
    echo "<h3>Found $postsWithNews news items in posts table</h3>";
    echo "<p>Would you like to import them into the news table?</p>";
    echo "<form method='post'>";
    echo "<button type='submit' name='import' value='1' style='background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;'>Import News from Posts</button>";
    echo "</form>";
    
    if (isset($_POST['import'])) {
        // Import news from posts
        $categoryMap = [
            'novosti-vuzov' => '1',
            'novosti-spo' => '2', 
            'novosti-shkol' => '3',
            'novosti-obrazovaniya' => '4'
        ];
        
        $imported = 0;
        foreach ($categoryMap as $postCat => $newsCat) {
            // Check if posts have the required columns
            $checkCols = $connection->query("SHOW COLUMNS FROM posts");
            $postCols = [];
            while ($col = $checkCols->fetch_assoc()) {
                $postCols[] = $col['Field'];
            }
            
            // Build import query based on available columns
            $titleCol = in_array('title_post', $postCols) ? 'title_post' : 'title';
            $textCol = in_array('text_post', $postCols) ? 'text_post' : 'text';
            $slugCol = in_array('url_slug', $postCols) ? 'url_slug' : (in_array('url_post', $postCols) ? 'url_post' : 'NULL');
            $dateCol = in_array('date_post', $postCols) ? 'date_post' : 'created_at';
            
            $importSQL = "INSERT INTO news (title_news, text_news, url_slug, date_news, category_news)
                         SELECT $titleCol, $textCol, $slugCol, $dateCol, '$newsCat'
                         FROM posts 
                         WHERE category_post = '$postCat'
                         AND NOT EXISTS (
                            SELECT 1 FROM news WHERE title_news = posts.$titleCol
                         )";
            
            if ($connection->query($importSQL)) {
                $affected = $connection->affected_rows;
                $imported += $affected;
                echo "<p>Imported $affected items from category '$postCat'</p>";
            } else {
                echo "<p style='color: orange;'>Warning importing $postCat: " . $connection->error . "</p>";
            }
        }
        
        echo "<p style='color: green;'><strong>✓ Total imported: $imported news items</strong></p>";
    }
}

// Final status
echo "<h2>Final Status</h2>";
$finalCount = $connection->query("SELECT COUNT(*) FROM news")->fetch_row()[0];
echo "<p>News table now has <strong>$finalCount</strong> records</p>";

if ($finalCount > 0) {
    echo "<p style='color: green; font-size: 18px;'>✅ News system is ready!</p>";
    echo "<p><a href='/news' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 10px;'>View News Page</a></p>";
} else {
    echo "<p style='color: orange;'>⚠️ News table is empty. You may need to add content or import from posts table.</p>";
}
?>