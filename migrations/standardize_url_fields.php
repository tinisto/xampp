<?php
// Migration: Standardize URL field names to url_slug
require_once __DIR__ . '/../config/loadEnv.php';

// Check if migration already ran
$migrationFile = __DIR__ . '/url_fields_migration_completed.txt';
if (file_exists($migrationFile)) {
    echo "❌ URL fields migration already completed. Skipping.\n";
    exit;
}

try {
    if (defined('DB_HOST') && defined('DB_USER') && defined('DB_PASS') && defined('DB_NAME')) {
        $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($connection->connect_error) {
            throw new Exception("Connection failed: " . $connection->connect_error);
        }
        
        $connection->set_charset("utf8mb4");
        echo "✅ Database connected\n";
    } else {
        throw new Exception("Database environment variables not set");
    }
} catch (Exception $e) {
    echo "❌ Connection error: " . $e->getMessage() . "\n";
    exit;
}

echo "🚀 Starting URL fields standardization migration...\n";

$migrations = [
    "news" => [
        "old_field" => "url_news",
        "description" => "Rename news.url_news to news.url_slug"
    ],
    "posts" => [
        "old_field" => "url_post", 
        "description" => "Rename posts.url_post to posts.url_slug"
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
        $checkNewQuery = "SHOW COLUMNS FROM `{$table}` LIKE 'url_slug'";
        $newResult = $connection->query($checkNewQuery);
        
        if ($newResult->num_rows > 0) {
            echo "⚠️  Field url_slug already exists in {$table} table - skipping\n";
            continue;
        }
        
        // Perform the rename
        $sql = "ALTER TABLE `{$table}` CHANGE COLUMN `{$migration['old_field']}` `url_slug` VARCHAR(255)";
        
        if ($connection->query($sql)) {
            echo "✅ Successfully renamed {$migration['old_field']} to url_slug in {$table} table\n";
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
    // Mark migration as completed
    file_put_contents($migrationFile, date('Y-m-d H:i:s') . " - URL fields migration completed successfully\n");
    echo "\n🎉 URL fields standardization migration completed successfully!\n";
    echo "📝 Migration marked as completed in: {$migrationFile}\n";
    
    echo "\n⚠️  IMPORTANT: You now need to update all PHP files that reference:\n";
    echo "   - url_news → url_slug (in news-related files)\n";
    echo "   - url_post → url_slug (in posts-related files)\n";
} else {
    echo "\n⚠️  Migration completed with {$errorCount} errors. Please review and fix before proceeding.\n";
}

$connection->close();
?>