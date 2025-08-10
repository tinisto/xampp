<?php
// Check what news-simple.php contains
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>News Simple File Analysis</h1>";

$newsSimplePath = $_SERVER['DOCUMENT_ROOT'] . '/news-simple.php';

if (file_exists($newsSimplePath)) {
    echo "<p style='color: green;'>✓ news-simple.php exists</p>";
    
    $content = file_get_contents($newsSimplePath);
    echo "<p>File size: " . strlen($content) . " characters</p>";
    
    echo "<h2>File Contents:</h2>";
    echo "<pre style='background: #f5f5f5; padding: 10px; border: 1px solid #ccc; max-height: 500px; overflow: auto;'>";
    echo htmlspecialchars($content);
    echo "</pre>";
    
    // Check if it's supposed to load news data
    if (strpos($content, 'Новости загружаются') !== false) {
        echo "<p style='color: red;'>⚠️ Found 'Новости загружаются' in news-simple.php</p>";
    }
    
    if (strpos($content, 'База данных новостей') !== false) {
        echo "<p style='color: red;'>⚠️ Found 'База данных новостей' in news-simple.php</p>";
    }
    
} else {
    echo "<p style='color: red;'>✗ news-simple.php does not exist at: $newsSimplePath</p>";
}

// Also check what news-new.php does when accessed directly
echo "<h2>Testing news-new.php Direct Access:</h2>";
$newsNewPath = $_SERVER['DOCUMENT_ROOT'] . '/news-new.php';
if (file_exists($newsNewPath)) {
    echo "<p style='color: green;'>✓ news-new.php exists</p>";
    echo "<p>Size: " . filesize($newsNewPath) . " bytes</p>";
    
    // Try to include it and capture output
    ob_start();
    try {
        include $newsNewPath;
        $output = ob_get_clean();
        echo "<p>Successfully included news-new.php</p>";
        echo "<p>Output length: " . strlen($output) . " characters</p>";
        
        // Check for key indicators
        if (strpos($output, 'card') !== false) {
            echo "<p style='color: green;'>✓ Contains card elements</p>";
        }
        if (strpos($output, '496') !== false) {
            echo "<p style='color: green;'>✓ Contains news count</p>";
        }
        
    } catch (Exception $e) {
        ob_end_clean();
        echo "<p style='color: red;'>Error including news-new.php: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>✗ news-new.php does not exist</p>";
}

echo "<h2>Solution:</h2>";
echo "<p>The .htaccess file routes /news to news-simple.php, but we need it to route to news-new.php</p>";
echo "<p>Either:</p>";
echo "<ol>";
echo "<li>Update .htaccess to route /news to news-new.php, OR</li>";
echo "<li>Update news-simple.php to include the working news content</li>";
echo "</ol>";
?>