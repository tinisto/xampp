#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import ftplib
import tempfile
import os

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def upload_file(ftp, content, filename):
    with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
        tmp.write(content)
        tmp_path = tmp.name
    
    with open(tmp_path, 'rb') as file:
        ftp.storbinary(f'STOR {filename}', file)
    os.unlink(tmp_path)

def main():
    print("🔍 TESTING DIRECT DATABASE CONNECTION")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Create a simple test page that bypasses the normal connection file
        test_page = '''<?php
// Direct database test - bypass normal connection
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Direct Database Connection Test</h1>";
echo "<pre>";

// Method 1: Try using the existing connection
echo "Method 1: Using existing db_connections.php\n";
echo "=====================================\n";
try {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
    
    if (isset($connection) && $connection) {
        echo "✓ Connection variable exists\n";
        
        // Test simple queries
        $tables = ['schools', 'vpo', 'spo', 'posts', 'users'];
        foreach ($tables as $table) {
            $result = @mysqli_query($connection, "SELECT COUNT(*) as cnt FROM $table LIMIT 1");
            if ($result) {
                $row = mysqli_fetch_assoc($result);
                echo "✓ Table '$table': " . $row['cnt'] . " records\n";
            } else {
                echo "✗ Table '$table': " . mysqli_error($connection) . "\n";
            }
        }
    } else {
        echo "✗ No connection variable found\n";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n\nMethod 2: Check config files\n";
echo "============================\n";

// Check if config exists
$configFile = $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
if (file_exists($configFile)) {
    echo "✓ Config file exists at: $configFile\n";
    
    // Check if .env exists
    $envFile = $_SERVER['DOCUMENT_ROOT'] . '/.env';
    if (file_exists($envFile)) {
        echo "✓ .env file exists\n";
        
        // Try to show non-sensitive info
        $envContent = file_get_contents($envFile);
        if (strpos($envContent, 'DB_HOST') !== false) {
            echo "✓ .env contains DB_HOST\n";
        }
        if (strpos($envContent, 'DB_NAME') !== false) {
            echo "✓ .env contains DB_NAME\n";
        }
    } else {
        echo "✗ .env file NOT found\n";
    }
} else {
    echo "✗ Config file NOT found\n";
}

echo "\n\nMethod 3: Check alternative connection files\n";
echo "==========================================\n";

$altConnections = [
    '/includes/db.php',
    '/includes/database.php',
    '/config/database.php',
    '/db_connect.php',
    '/database/connection.php',
    '/database/db_connect.php'
];

foreach ($altConnections as $file) {
    $fullPath = $_SERVER['DOCUMENT_ROOT'] . $file;
    if (file_exists($fullPath)) {
        echo "✓ Found: $file\n";
    }
}

echo "\n\nMethod 4: Check working dashboard pages\n";
echo "=====================================\n";

// Check if dashboard pages have different connection
$dashboardFiles = [
    '/dashboard-news-functional.php',
    '/dashboard-vpo-functional.php',
    '/dashboard-schools-functional.php'
];

foreach ($dashboardFiles as $file) {
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . $file)) {
        echo "✓ Dashboard file exists: $file\n";
    }
}

echo "</pre>";

// Try one more direct connection attempt
echo "<h2>Direct Connection Attempt</h2>";
echo "<pre>";

// Look for credentials in common places
$possibleConfigs = [
    $_SERVER['DOCUMENT_ROOT'] . '/config.php',
    $_SERVER['DOCUMENT_ROOT'] . '/wp-config.php',
    $_SERVER['DOCUMENT_ROOT'] . '/configuration.php'
];

foreach ($possibleConfigs as $config) {
    if (file_exists($config)) {
        echo "Found config file: " . basename($config) . "\n";
    }
}

echo "</pre>";
?>'''
        
        upload_file(ftp, test_page, 'test-db-direct.php')
        print("   ✅ Created direct database test page")
        
        ftp.quit()
        
        print("\n✅ Test page created!")
        print("\n🎯 Visit: https://11klassniki.ru/test-db-direct.php")
        print("\nThis will show:")
        print("• Connection status")
        print("• Table record counts")
        print("• Config file locations")
        print("• Alternative connection methods")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()