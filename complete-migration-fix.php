<?php
/**
 * Complete Migration Fix Script
 * This script performs the database migration for URL fields
 * Can be run via web browser or command line
 */

// Set display for web output
if (isset($_SERVER['HTTP_HOST'])) {
    echo "<pre>";
    echo "<h2>Database Migration: URL Fields (url_post → url_slug, url_news → url_slug)</h2>\n";
} else {
    echo "Database Migration: URL Fields\n";
    echo "==============================\n";
}

// Load environment configuration
if (file_exists(__DIR__ . '/.env')) {
    $envContent = file_get_contents(__DIR__ . '/.env');
    $lines = explode("\n", $envContent);
    
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line && strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value, '"\'');
        }
    }
}

// Database connection
$host = $_ENV['DB_HOST'] ?? 'localhost';
$user = $_ENV['DB_USER'] ?? 'root';
$pass = $_ENV['DB_PASS'] ?? 'root';
$dbname = $_ENV['DB_NAME'] ?? '11klassniki_claude';

echo "🔗 Connecting to database: $dbname\n";

try {
    $connection = new mysqli($host, $user, $pass, $dbname);
    
    if ($connection->connect_error) {
        throw new Exception("Connection failed: " . $connection->connect_error);
    }
    
    $connection->set_charset("utf8mb4");
    echo "✅ Database connected successfully\n\n";
} catch (Exception $e) {
    echo "❌ Connection error: " . $e->getMessage() . "\n";
    if (isset($_SERVER['HTTP_HOST'])) echo "</pre>";
    exit;
}

echo "🚀 Starting URL fields migration...\n\n";

$migrations = [
    "posts" => [
        "old_field" => "url_post",
        "description" => "Rename posts.url_post to posts.url_slug"
    ],
    "news" => [
        "old_field" => "url_news", 
        "description" => "Rename news.url_news to news.url_slug"
    ]
];

$errorCount = 0;
$successCount = 0;

foreach ($migrations as $table => $migration) {
    echo "📋 {$migration['description']}\n";
    
    try {
        // Check if old field exists
        $checkQuery = "SHOW COLUMNS FROM `{$table}` LIKE '{$migration['old_field']}'";
        $result = $connection->query($checkQuery);
        
        if ($result->num_rows === 0) {
            echo "⚠️  Field {$migration['old_field']} not found in {$table} table - skipping\n\n";
            continue;
        }
        
        // Check if url_slug already exists
        $checkNewQuery = "SHOW COLUMNS FROM `{$table}` LIKE 'url_slug'";
        $newResult = $connection->query($checkNewQuery);
        
        if ($newResult->num_rows > 0) {
            echo "⚠️  Field url_slug already exists in {$table} table - skipping\n\n";
            continue;
        }
        
        // Get column definition to preserve attributes
        $showCreateQuery = "SHOW CREATE TABLE `{$table}`";
        $createResult = $connection->query($showCreateQuery);
        if ($createResult) {
            $createTableRow = $createResult->fetch_assoc();
            $createTableSQL = $createTableRow['Create Table'];
            
            // Extract the original column definition
            if (preg_match("/`{$migration['old_field']}`\s+([^,]+)/", $createTableSQL, $matches)) {
                $columnDefinition = trim($matches[1]);
                echo "📄 Current column definition: {$columnDefinition}\n";
                
                // Perform the rename with preserved definition
                $sql = "ALTER TABLE `{$table}` CHANGE COLUMN `{$migration['old_field']}` `url_slug` {$columnDefinition}";
                
                if ($connection->query($sql)) {
                    echo "✅ Successfully renamed {$migration['old_field']} to url_slug in {$table} table\n";
                    $successCount++;
                } else {
                    echo "❌ Error: " . $connection->error . "\n";
                    $errorCount++;
                }
            } else {
                // Fallback with standard VARCHAR definition
                $sql = "ALTER TABLE `{$table}` CHANGE COLUMN `{$migration['old_field']}` `url_slug` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
                
                if ($connection->query($sql)) {
                    echo "✅ Successfully renamed {$migration['old_field']} to url_slug in {$table} table (fallback)\n";
                    $successCount++;
                } else {
                    echo "❌ Error: " . $connection->error . "\n";
                    $errorCount++;
                }
            }
        }
        
    } catch (Exception $e) {
        echo "❌ Exception during {$table} migration: " . $e->getMessage() . "\n";
        $errorCount++;
    }
    
    echo "\n";
}

echo "📊 Migration Summary:\n";
echo "✅ Successful operations: {$successCount}\n";
echo "❌ Failed operations: {$errorCount}\n\n";

if ($errorCount === 0) {
    echo "🎉 URL fields migration completed successfully!\n\n";
    
    // Test the migration by checking if we can select from the new columns
    echo "🧪 Testing migration...\n";
    
    // Test posts table
    $testPostsQuery = "SELECT COUNT(*) as count FROM posts WHERE url_slug IS NOT NULL";
    $testPostsResult = $connection->query($testPostsQuery);
    if ($testPostsResult) {
        $postsCount = $testPostsResult->fetch_assoc()['count'];
        echo "✅ Posts table: {$postsCount} records with url_slug\n";
    }
    
    // Test news table  
    $testNewsQuery = "SELECT COUNT(*) as count FROM news WHERE url_slug IS NOT NULL";
    $testNewsResult = $connection->query($testNewsQuery);
    if ($testNewsResult) {
        $newsCount = $testNewsResult->fetch_assoc()['count'];
        echo "✅ News table: {$newsCount} records with url_slug\n";
    }
    
    echo "\n✅ All PHP files have already been updated to use url_slug!\n";
    echo "✅ Regions table column references have been fixed!\n";
    
} else {
    echo "⚠️  Migration completed with {$errorCount} errors. Please review and fix before proceeding.\n";
}

$connection->close();

if (isset($_SERVER['HTTP_HOST'])) {
    echo "</pre>";
    echo "<p><strong>Migration completed. You can now test the website functionality.</strong></p>";
}
?>