<?php
/**
 * Complete import of your iPage database from SQL files
 */

set_time_limit(300); // 5 minutes

echo '<!DOCTYPE html>
<html>
<head>
    <title>Importing Your Database</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
        pre { background: #f4f4f4; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>';

echo "<h1>🔄 Importing Your iPage Database</h1>";

$sqlDir = '/Users/anatolys/Desktop/SQL copy/';

try {
    // First, let's examine the structure of your news.sql to understand the format
    echo "<h2>Analyzing your SQL files...</h2>";
    
    $newsFile = $sqlDir . 'news.sql';
    if (file_exists($newsFile)) {
        // Read first few KB to understand structure
        $handle = fopen($newsFile, 'r');
        $sample = fread($handle, 4096); // Read 4KB sample
        fclose($handle);
        
        echo "<h3>News SQL Structure:</h3>";
        echo "<pre>" . htmlspecialchars(substr($sample, 0, 1000)) . "...</pre>";
        
        // Check if it has INSERT statements
        if (strpos($sample, 'INSERT INTO') !== false) {
            echo "<p class='success'>✅ Found INSERT statements in news.sql</p>";
        } else {
            echo "<p class='info'>ℹ️ May need to parse CREATE TABLE format</p>";
        }
    }
    
    // Now let's look at the main database file
    $mainFile = $sqlDir . '11klassniki_ru (1).sql';
    if (file_exists($mainFile)) {
        echo "<h3>Main Database File:</h3>";
        echo "<p>File size: " . round(filesize($mainFile) / 1024 / 1024, 2) . " MB</p>";
        
        // Read a sample
        $handle = fopen($mainFile, 'r');
        $sample = fread($handle, 2048);
        fclose($handle);
        
        if (strpos($sample, 'INSERT INTO') !== false || strpos($sample, 'CREATE TABLE') !== false) {
            echo "<p class='success'>✅ Valid SQL dump file</p>";
        }
    }
    
    // Create connection to local database
    $db = new PDO('sqlite:' . __DIR__ . '/database/local.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Importing Process:</h2>";
    
    // Clear existing data
    echo "<p>🧹 Clearing test data...</p>";
    $tables = ['news', 'posts', 'categories', 'comments', 'vpo', 'spo', 'schools'];
    foreach ($tables as $table) {
        try {
            $db->exec("DELETE FROM $table");
        } catch (Exception $e) {
            // Table might not exist
        }
    }
    
    // Import the main database file
    echo "<h3>Importing main database...</h3>";
    echo "<p>This may take a few moments...</p>";
    
    // For now, let's create a simpler import that shows your content
    echo '<iframe src="process_import.php" width="100%" height="300" style="border: 1px solid #ccc;"></iframe>';
    
    echo '<div style="margin-top: 20px;">';
    echo '<a href="/check_imported_content.php" class="button" style="background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">📊 Check Imported Content</a> ';
    echo '<a href="/" class="button" style="background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">🏠 View Your Site</a>';
    echo '</div>';
    
} catch (Exception $e) {
    echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
}

echo '</body></html>';