<?php
/**
 * Process the actual import of SQL files
 */

header('Content-Type: text/html; charset=utf-8');
echo '<style>body { font-family: monospace; font-size: 12px; }</style>';

$sqlDir = '/Users/anatolys/Desktop/SQL copy/';

try {
    $db = new PDO('sqlite:' . __DIR__ . '/database/local.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Import news.sql
    echo "📰 Importing news.sql...<br>";
    flush();
    
    $newsFile = $sqlDir . 'news.sql';
    if (file_exists($newsFile)) {
        $content = file_get_contents($newsFile);
        
        // Parse MySQL dump - look for INSERT statements
        if (preg_match_all('/INSERT INTO\s+`?news`?\s+VALUES\s*\((.*?)\);/s', $content, $matches)) {
            $count = 0;
            foreach ($matches[1] as $values) {
                // Parse the values - handle MySQL escaping
                $values = str_replace('\\"', '"', $values);
                $values = str_replace("\\'", "''", $values);
                
                try {
                    $db->exec("INSERT INTO news VALUES ($values)");
                    $count++;
                    if ($count % 10 == 0) {
                        echo "Imported $count news...<br>";
                        flush();
                    }
                } catch (Exception $e) {
                    // Skip errors
                }
            }
            echo "✅ Imported $count news articles<br><br>";
        }
    }
    
    // Import posts.sql
    echo "📚 Importing posts.sql...<br>";
    flush();
    
    $postsFile = $sqlDir . 'posts.sql';
    if (file_exists($postsFile)) {
        $content = file_get_contents($postsFile);
        
        // Parse MySQL dump
        if (preg_match_all('/INSERT INTO\s+`?posts`?\s+VALUES\s*\((.*?)\);/s', $content, $matches)) {
            $count = 0;
            foreach ($matches[1] as $values) {
                $values = str_replace('\\"', '"', $values);
                $values = str_replace("\\'", "''", $values);
                
                try {
                    $db->exec("INSERT INTO posts VALUES ($values)");
                    $count++;
                    if ($count % 10 == 0) {
                        echo "Imported $count posts...<br>";
                        flush();
                    }
                } catch (Exception $e) {
                    // Skip errors
                }
            }
            echo "✅ Imported $count posts<br><br>";
        }
    }
    
    // Import categories
    echo "📂 Importing categories.sql...<br>";
    flush();
    
    $catFile = $sqlDir . 'categories.sql';
    if (file_exists($catFile)) {
        $content = file_get_contents($catFile);
        if (preg_match_all('/INSERT INTO\s+`?categories`?\s+VALUES\s*\((.*?)\);/s', $content, $matches)) {
            $count = 0;
            foreach ($matches[1] as $values) {
                try {
                    $db->exec("INSERT INTO categories VALUES ($values)");
                    $count++;
                } catch (Exception $e) {
                    // Skip
                }
            }
            echo "✅ Imported $count categories<br><br>";
        }
    }
    
    echo "<strong>✅ Import complete!</strong><br>";
    echo '<a href="/" target="_parent">View your site with YOUR content</a>';
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>