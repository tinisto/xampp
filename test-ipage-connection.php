<?php
// Test iPage Database Connection
// This file verifies the database connection is using the correct iPage MySQL host

echo "<!DOCTYPE html>\n";
echo "<html><head><title>iPage Database Connection Test</title></head><body>\n";
echo "<h1>iPage Database Connection Test</h1>\n";
echo "<pre>\n";

// Show current environment
echo "=== ENVIRONMENT CHECK ===\n";
echo "Script Location: " . __FILE__ . "\n";
echo "Server: " . ($_SERVER['SERVER_NAME'] ?? 'Unknown') . "\n";
echo "Is Local: " . (strpos($_SERVER['SERVER_NAME'] ?? '', 'localhost') !== false ? 'Yes' : 'No') . "\n\n";

// Check if .env file exists
echo "=== .ENV FILE CHECK ===\n";
$envPath = __DIR__ . '/.env';
if (file_exists($envPath)) {
    echo "✓ .env file found at: $envPath\n";
    
    // Read and display DB_HOST from .env
    $envContent = file_get_contents($envPath);
    if (preg_match('/DB_HOST=(.+)/', $envContent, $matches)) {
        echo "✓ DB_HOST in .env: " . trim($matches[1]) . "\n";
    }
} else {
    echo "✗ .env file NOT found!\n";
}
echo "\n";

// Load environment
echo "=== LOADING ENVIRONMENT ===\n";
require_once __DIR__ . '/config/loadEnv.php';

// Show loaded configuration
echo "=== LOADED CONFIGURATION ===\n";
echo "DB_HOST: " . (defined('DB_HOST') ? DB_HOST : 'NOT DEFINED') . "\n";
echo "DB_USER: " . (defined('DB_USER') ? DB_USER : 'NOT DEFINED') . "\n";
echo "DB_NAME: " . (defined('DB_NAME') ? DB_NAME : 'NOT DEFINED') . "\n";
echo "DB_PASS: " . (defined('DB_PASS') ? '***hidden***' : 'NOT DEFINED') . "\n\n";

// Check if it's the correct iPage host
echo "=== HOST VERIFICATION ===\n";
$expectedHost = '11klassnikiru67871.ipagemysql.com';
if (defined('DB_HOST')) {
    if (DB_HOST === $expectedHost) {
        echo "✅ CORRECT: Using iPage MySQL host!\n";
    } else if (DB_HOST === 'localhost' || DB_HOST === '127.0.0.1') {
        echo "❌ ERROR: Still using localhost! PHP cache needs to be cleared.\n";
        echo "   Expected: $expectedHost\n";
        echo "   Current: " . DB_HOST . "\n";
    } else {
        echo "⚠️  WARNING: Using different host: " . DB_HOST . "\n";
    }
} else {
    echo "❌ ERROR: DB_HOST not defined!\n";
}
echo "\n";

// Try to connect
echo "=== CONNECTION TEST ===\n";
if (defined('DB_HOST') && defined('DB_USER') && defined('DB_PASS') && defined('DB_NAME')) {
    try {
        $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($connection->connect_error) {
            echo "❌ Connection FAILED: " . $connection->connect_error . "\n";
        } else {
            echo "✅ Connection SUCCESSFUL!\n";
            echo "   Server: " . $connection->server_info . "\n";
            echo "   Host Info: " . $connection->host_info . "\n";
            
            // Test query
            $result = $connection->query("SELECT DATABASE() as db");
            if ($result) {
                $row = $result->fetch_assoc();
                echo "   Current Database: " . $row['db'] . "\n";
            }
            
            $connection->close();
        }
    } catch (Exception $e) {
        echo "❌ Connection ERROR: " . $e->getMessage() . "\n";
    }
} else {
    echo "❌ Cannot test connection - configuration not loaded\n";
}

echo "\n=== NEXT STEPS ===\n";
if (defined('DB_HOST') && (DB_HOST === 'localhost' || DB_HOST === '127.0.0.1')) {
    echo "1. The .env file has been updated but PHP is caching old values\n";
    echo "2. Contact iPage support to restart PHP-FPM\n";
    echo "3. Or wait 2-6 hours for cache to expire\n";
    echo "4. Visit: https://11klassniki.ru/ipage_support_guide.php for help\n";
} else if (defined('DB_HOST') && DB_HOST === $expectedHost) {
    echo "✅ Everything looks good! The correct iPage host is configured.\n";
} else {
    echo "1. Upload the .env file to your server\n";
    echo "2. Make sure it contains: DB_HOST=$expectedHost\n";
}

echo "</pre>\n";
echo "</body></html>";
?>