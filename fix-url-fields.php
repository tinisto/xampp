<?php
// Simple migration to fix url field names
// This script can be run from command line or web

// Set document root if not set (for CLI)
if (!isset($_SERVER['DOCUMENT_ROOT'])) {
    $_SERVER['DOCUMENT_ROOT'] = __DIR__;
}

// Database connection
$host = 'localhost';
$user = 'root';
$pass = 'root';
$dbname = '11klassniki_claude';

try {
    $connection = new mysqli($host, $user, $pass, $dbname);
    
    if ($connection->connect_error) {
        throw new Exception("Connection failed: " . $connection->connect_error);
    }
    
    $connection->set_charset("utf8mb4");
    echo "✅ Database connected to {$dbname}\n";
} catch (Exception $e) {
    echo "❌ Connection error: " . $e->getMessage() . "\n";
    exit;
}

echo "🚀 Starting URL fields migration...\n";

$migrations = [
    "posts" => [
        "old_field" => "url_post",
        "new_field" => "url_slug",
        "description" => "Rename posts.url_post to posts.url_slug"
    ],
    "news" => [
        "old_field" => "url_news", 
        "new_field" => "url_slug",
        "description" => "Rename news.url_news to news.url_slug"
    ]
];

$errorCount = 0;
$successCount = 0;

foreach ($migrations as $table => $migration) {
    echo "\n📋 {$migration['description']}\n";
    
    try {
        // Check if old field exists
        $checkQuery = "SHOW COLUMNS FROM `{$table}` LIKE '{$migration['old_field']}'";
        $result = $connection->query($checkQuery);
        
        if ($result->num_rows === 0) {
            echo "⚠️  Field {$migration['old_field']} not found in {$table} table - skipping\n";
            continue;
        }
        
        // Check if url_slug already exists
        $checkNewQuery = "SHOW COLUMNS FROM `{$table}` LIKE '{$migration['new_field']}'";
        $newResult = $connection->query($checkNewQuery);
        
        if ($newResult->num_rows > 0) {
            echo "⚠️  Field {$migration['new_field']} already exists in {$table} table - skipping\n";
            continue;
        }
        
        // Get the column definition
        $getDefQuery = "SHOW CREATE TABLE `{$table}`";
        $defResult = $connection->query($getDefQuery);
        if ($defResult) {
            $createTable = $defResult->fetch_assoc()['Create Table'];
            echo "📄 Current table structure checked\n";
        }
        
        // Perform the rename - use CHANGE to preserve all attributes 
        $sql = "ALTER TABLE `{$table}` CHANGE COLUMN `{$migration['old_field']}` `{$migration['new_field']}` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        
        if ($connection->query($sql)) {
            echo "✅ Successfully renamed {$migration['old_field']} to {$migration['new_field']} in {$table} table\n";
            $successCount++;
        } else {
            echo "❌ Error: " . $connection->error . "\n";
            $errorCount++;
        }
        
    } catch (Exception $e) {
        echo "❌ Exception during {$table} migration: " . $e->getMessage() . "\n";
        $errorCount++;
    }
}

echo "\n📊 Migration Summary:\n";
echo "✅ Successful operations: {$successCount}\n";
echo "❌ Failed operations: {$errorCount}\n";

if ($errorCount === 0) {
    echo "\n🎉 URL fields migration completed successfully!\n";
    echo "\n⚠️  NEXT STEPS: Update PHP files to use url_slug instead of:\n";
    echo "   - url_post (in posts-related files)\n";
    echo "   - url_news (in news-related files)\n";
} else {
    echo "\n⚠️  Migration completed with {$errorCount} errors. Please review and fix before proceeding.\n";
}

$connection->close();
?>