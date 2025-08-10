<?php
/**
 * Import YOUR iPage Database Export
 * This will replace test content with YOUR actual content
 */

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Import iPage Database</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .info { color: #17a2b8; }
        .step {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
        }
        code {
            background: #e9ecef;
            padding: 2px 5px;
            border-radius: 3px;
        }
        .button {
            background: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📥 Import Your iPage Database</h1>
        
        <?php
        $exportFile = __DIR__ . '/ipage_export.sql';
        
        if (file_exists($exportFile)) {
            echo '<div class="success">';
            echo '<h2>✅ Found ipage_export.sql!</h2>';
            echo '<p>File size: ' . number_format(filesize($exportFile) / 1024, 2) . ' KB</p>';
            echo '</div>';
            
            if (isset($_POST['import'])) {
                try {
                    // Connect to SQLite
                    $db = new PDO('sqlite:' . __DIR__ . '/database/local.sqlite');
                    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    
                    echo '<h3>Importing your data...</h3>';
                    
                    // Clear existing test data
                    echo '<p class="info">🧹 Clearing test data...</p>';
                    $db->exec("DELETE FROM news");
                    $db->exec("DELETE FROM posts");
                    $db->exec("DELETE FROM comments");
                    
                    // Read SQL file
                    $sql = file_get_contents($exportFile);
                    
                    // Process SQL for SQLite compatibility
                    // Remove MySQL-specific syntax
                    $sql = preg_replace('/ENGINE=InnoDB.*?;/i', ';', $sql);
                    $sql = preg_replace('/DEFAULT CHARSET=.*?;/i', ';', $sql);
                    $sql = preg_replace('/AUTO_INCREMENT=\d+/i', '', $sql);
                    
                    // Split into individual statements
                    $statements = array_filter(explode(';', $sql));
                    
                    $imported = 0;
                    foreach ($statements as $statement) {
                        $statement = trim($statement);
                        if (!empty($statement)) {
                            try {
                                $db->exec($statement);
                                $imported++;
                            } catch (Exception $e) {
                                // Log but continue
                                echo '<p class="error">⚠️ Skipped: ' . substr($statement, 0, 50) . '...</p>';
                            }
                        }
                    }
                    
                    echo '<div class="success">';
                    echo '<h3>✅ Import Complete!</h3>';
                    echo '<p>Processed ' . $imported . ' SQL statements</p>';
                    echo '</div>';
                    
                    // Show what was imported
                    $newsCount = $db->query("SELECT COUNT(*) FROM news")->fetchColumn();
                    $postsCount = $db->query("SELECT COUNT(*) FROM posts")->fetchColumn();
                    
                    echo '<h3>Your Content:</h3>';
                    echo '<ul>';
                    echo '<li>📰 News articles: <strong>' . $newsCount . '</strong></li>';
                    echo '<li>📚 Posts/Articles: <strong>' . $postsCount . '</strong></li>';
                    echo '</ul>';
                    
                    echo '<p><a href="/" class="button">🏠 View Your Site</a></p>';
                    
                } catch (Exception $e) {
                    echo '<div class="error">';
                    echo '<h3>❌ Import Error</h3>';
                    echo '<p>' . $e->getMessage() . '</p>';
                    echo '</div>';
                }
            } else {
                ?>
                <form method="post">
                    <p>Click the button below to import YOUR content from iPage:</p>
                    <button type="submit" name="import" class="button">
                        🚀 Start Import
                    </button>
                </form>
                <?php
            }
            
        } else {
            echo '<div class="error">';
            echo '<h2>❌ ipage_export.sql not found</h2>';
            echo '</div>';
            
            echo '<h3>📋 How to export from iPage:</h3>';
            echo '<ol>';
            echo '<li>Log into your <strong>iPage Control Panel</strong></li>';
            echo '<li>Open <strong>phpMyAdmin</strong></li>';
            echo '<li>Select database: <code>11klassniki_claude</code></li>';
            echo '<li>Click <strong>Export</strong> tab</li>';
            echo '<li>Choose <strong>Quick</strong> export, format: <strong>SQL</strong></li>';
            echo '<li>Click <strong>Go</strong> to download</li>';
            echo '<li>Save as <code>ipage_export.sql</code> in:<br><code>' . __DIR__ . '/</code></li>';
            echo '</ol>';
            
            echo '<div class="step">';
            echo '<h3>🎯 Quick Test: Add Sample Content</h3>';
            echo '<p>Want to test with a few items first? Create a file called <code>sample_content.txt</code> with:</p>';
            echo '<pre>';
            echo 'NEWS: Your First News Title | Your news content here...
NEWS: Your Second News Title | More news content...
POST: Your First Article | Your article content here...
POST: Your Second Article | More article content...';
            echo '</pre>';
            echo '<p>I can import this format quickly for testing.</p>';
            echo '</div>';
        }
        
        // Check for sample content file
        $sampleFile = __DIR__ . '/sample_content.txt';
        if (file_exists($sampleFile) && !isset($_POST['import'])) {
            echo '<div class="info">';
            echo '<h3>📄 Found sample_content.txt</h3>';
            echo '<form method="post">';
            echo '<button type="submit" name="import_sample" class="button">Import Sample Content</button>';
            echo '</form>';
            echo '</div>';
            
            if (isset($_POST['import_sample'])) {
                // Import sample content
                $lines = file($sampleFile);
                $db = new PDO('sqlite:' . __DIR__ . '/database/local.sqlite');
                
                foreach ($lines as $line) {
                    if (strpos($line, 'NEWS:') === 0) {
                        list($type, $content) = explode(':', $line, 2);
                        list($title, $text) = explode('|', $content, 2);
                        
                        $stmt = $db->prepare("INSERT INTO news (title_news, text_news, url_news, is_published, created_at) VALUES (?, ?, ?, 1, datetime('now'))");
                        $stmt->execute([trim($title), trim($text), 'news-' . time() . rand(100,999)]);
                    }
                    elseif (strpos($line, 'POST:') === 0) {
                        list($type, $content) = explode(':', $line, 2);
                        list($title, $text) = explode('|', $content, 2);
                        
                        $stmt = $db->prepare("INSERT INTO posts (title_post, text_post, url_slug, is_published, date_post) VALUES (?, ?, ?, 1, datetime('now'))");
                        $stmt->execute([trim($title), trim($text), 'post-' . time() . rand(100,999)]);
                    }
                }
                
                echo '<p class="success">✅ Sample content imported!</p>';
            }
        }
        ?>
    </div>
</body>
</html>