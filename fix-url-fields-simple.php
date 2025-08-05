<?php
// Simple URL fields migration - compatible with server environment
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Database Migration: URL Fields</h1>";
echo "<p>Standardizing url_post → url_slug and url_news → url_slug</p>";

// Try different connection methods
$connection = null;
$connection_method = '';

// Method 1: Try with localhost
try {
    $connection = new mysqli('localhost', 'franko', 'JyvR!HK2E!N55Zt', '11klassniki_claude');
    if (!$connection->connect_error) {
        $connection_method = 'localhost';
        echo "<p>✅ Connected via localhost</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ localhost failed: " . $e->getMessage() . "</p>";
}

// Method 2: Try with IP if localhost failed
if (!$connection || $connection->connect_error) {
    try {
        $connection = new mysqli('127.0.0.1', 'franko', 'JyvR!HK2E!N55Zt', '11klassniki_claude');
        if (!$connection->connect_error) {
            $connection_method = '127.0.0.1';
            echo "<p>✅ Connected via 127.0.0.1</p>";
        }
    } catch (Exception $e) {
        echo "<p>❌ 127.0.0.1 failed: " . $e->getMessage() . "</p>";
    }
}

// Method 3: Try with socket path
if (!$connection || $connection->connect_error) {
    try {
        $connection = new mysqli('localhost', 'franko', 'JyvR!HK2E!N55Zt', '11klassniki_claude', 3306, '/var/lib/mysql/mysql.sock');
        if (!$connection->connect_error) {
            $connection_method = 'socket';
            echo "<p>✅ Connected via socket</p>";
        }
    } catch (Exception $e) {
        echo "<p>❌ socket failed: " . $e->getMessage() . "</p>";
    }
}

if (!$connection || $connection->connect_error) {
    echo "<h2>❌ All connection methods failed</h2>";
    echo "<p>Unable to connect to database. Please check server configuration.</p>";
    exit;
}

$connection->set_charset("utf8mb4");

echo "<h2>🚀 Starting Migration</h2>";

$migrations = [
    'posts' => [
        'old_field' => 'url_post',
        'new_field' => 'url_slug',
        'description' => 'Posts table: url_post → url_slug'
    ],
    'news' => [
        'old_field' => 'url_news', 
        'new_field' => 'url_slug',
        'description' => 'News table: url_news → url_slug'
    ]
];

$success_count = 0;
$error_count = 0;

foreach ($migrations as $table => $migration) {
    echo "<h3>📋 {$migration['description']}</h3>";
    
    try {
        // Check if old field exists
        $check_sql = "SHOW COLUMNS FROM `{$table}` LIKE '{$migration['old_field']}'";
        $result = $connection->query($check_sql);
        
        if ($result->num_rows === 0) {
            echo "<p>⚠️ Field {$migration['old_field']} not found in {$table} - skipping</p>";
            continue;
        }
        
        // Check if new field already exists
        $check_new_sql = "SHOW COLUMNS FROM `{$table}` LIKE '{$migration['new_field']}'";
        $new_result = $connection->query($check_new_sql);
        
        if ($new_result->num_rows > 0) {
            echo "<p>⚠️ Field {$migration['new_field']} already exists in {$table} - skipping</p>";
            continue;
        }
        
        // Perform the rename
        $alter_sql = "ALTER TABLE `{$table}` CHANGE COLUMN `{$migration['old_field']}` `{$migration['new_field']}` VARCHAR(255)";
        
        if ($connection->query($alter_sql)) {
            echo "<p>✅ Successfully renamed {$migration['old_field']} to {$migration['new_field']} in {$table}</p>";
            $success_count++;
        } else {
            echo "<p>❌ Error: " . $connection->error . "</p>";
            $error_count++;
        }
        
    } catch (Exception $e) {
        echo "<p>❌ Exception: " . $e->getMessage() . "</p>";
        $error_count++;
    }
}

echo "<h2>📊 Migration Summary</h2>";
echo "<p>✅ Successful operations: {$success_count}</p>";
echo "<p>❌ Failed operations: {$error_count}</p>";

if ($error_count === 0) {
    echo "<h2>🎉 Migration Completed Successfully!</h2>";
    echo "<p>All URL fields have been standardized to use 'url_slug'</p>";
    echo "<p><strong>Next step:</strong> Test comment submission at your post pages</p>";
} else {
    echo "<h2>⚠️ Migration completed with errors</h2>";
    echo "<p>Please review the errors above and run again if needed</p>";
}

$connection->close();
?>