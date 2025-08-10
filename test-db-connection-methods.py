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
    print("🔧 TESTING DATABASE CONNECTION METHODS")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # 1. Create a test that tries multiple connection methods
        print("\n1️⃣ Creating connection test page...")
        
        test_page = '''<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Database Connection Test</h1>";
echo "<pre>";

// Method 1: Check if .env file exists
echo "1. Checking .env file:\n";
$envPath = $_SERVER['DOCUMENT_ROOT'] . '/.env';
if (file_exists($envPath)) {
    echo "   ✓ .env file exists\n";
    // Don't show contents for security
} else {
    echo "   ✗ .env file NOT found at: $envPath\n";
}

// Method 2: Check config directory
echo "\n2. Checking config directory:\n";
$configDir = $_SERVER['DOCUMENT_ROOT'] . '/config';
if (is_dir($configDir)) {
    echo "   ✓ /config directory exists\n";
    $files = scandir($configDir);
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            echo "   - Found: $file\n";
        }
    }
} else {
    echo "   ✗ /config directory NOT found\n";
}

// Method 3: Check common database files
echo "\n3. Looking for database connection files:\n";
$dbFiles = [
    '/database/db_connections.php',
    '/includes/db.php',
    '/includes/database.php',
    '/config/database.php',
    '/db_connect.php',
    '/db.php'
];

foreach ($dbFiles as $file) {
    $path = $_SERVER['DOCUMENT_ROOT'] . $file;
    if (file_exists($path)) {
        echo "   ✓ Found: $file (size: " . filesize($path) . " bytes)\n";
    }
}

// Method 4: Try to include and test connection
echo "\n4. Testing db_connections.php:\n";
try {
    // Suppress errors temporarily
    $old_error = error_reporting(0);
    
    // Check if constants are defined
    if (!defined('DB_HOST')) {
        echo "   ⚠️  DB_HOST not defined before include\n";
    }
    
    // Try to include
    include_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
    
    error_reporting($old_error);
    
    if (isset($connection)) {
        echo "   ✓ \$connection variable exists\n";
        if ($connection instanceof mysqli) {
            echo "   ✓ Connection is mysqli object\n";
            echo "   ✓ Connection status: " . ($connection->ping() ? "ALIVE" : "DEAD") . "\n";
        } else {
            echo "   ✗ \$connection is not mysqli object\n";
        }
    } else {
        echo "   ✗ \$connection variable NOT set\n";
    }
    
    // Check if constants got defined
    if (defined('DB_HOST')) {
        echo "   ✓ DB_HOST is defined\n";
    } else {
        echo "   ✗ DB_HOST still not defined\n";
    }
    
} catch (Exception $e) {
    echo "   ✗ Error including db_connections.php: " . $e->getMessage() . "\n";
}

// Method 5: Check working files
echo "\n5. Checking working dashboard files:\n";
$workingFiles = [
    '/dashboard-news-functional.php',
    '/dashboard-schools-functional.php',
    '/approve_news.php'
];

foreach ($workingFiles as $file) {
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . $file)) {
        echo "   ✓ Found working file: $file\n";
        
        // Check first few lines to see how they connect
        $content = file_get_contents($_SERVER['DOCUMENT_ROOT'] . $file);
        if (strpos($content, 'db_connections.php') !== false) {
            echo "     - Uses db_connections.php\n";
        }
        if (strpos($content, 'db.php') !== false) {
            echo "     - Uses db.php\n";
        }
        if (strpos($content, 'mysqli_connect') !== false) {
            echo "     - Uses direct mysqli_connect\n";
        }
    }
}

// Method 6: Look for alternative connection in includes
echo "\n6. Checking /includes directory:\n";
$includesDir = $_SERVER['DOCUMENT_ROOT'] . '/includes';
if (is_dir($includesDir)) {
    $files = scandir($includesDir);
    foreach ($files as $file) {
        if (strpos($file, 'db') !== false || strpos($file, 'database') !== false || strpos($file, 'config') !== false) {
            echo "   - Found: $file\n";
        }
    }
}

echo "</pre>";

// Try a simple connection test
echo "<h2>Direct Connection Test</h2>";
echo "<pre>";

// Look for working connection in a dashboard file
$testFile = $_SERVER['DOCUMENT_ROOT'] . '/dashboard-news-functional.php';
if (file_exists($testFile)) {
    echo "Examining working dashboard file...\n";
    $content = file_get_contents($testFile);
    
    // Extract connection method
    if (preg_match('/require.*?\((.*?db.*?\.php.*?)\)/', $content, $matches)) {
        echo "Dashboard uses: " . $matches[1] . "\n";
    }
}

echo "</pre>";
?>'''
        
        upload_file(ftp, test_page, 'test-connection.php')
        print("   ✅ Created connection test page")
        
        # 2. Check a working dashboard file to see how it connects
        print("\n2️⃣ Downloading a working dashboard file to analyze...")
        
        try:
            with tempfile.NamedTemporaryFile(delete=False) as tmp:
                tmp_path = tmp.name
            
            ftp.retrbinary('RETR dashboard-news-functional.php', open(tmp_path, 'wb').write)
            
            with open(tmp_path, 'r', encoding='utf-8') as f:
                content = f.read()[:500]  # First 500 chars
            
            print("\n   Dashboard file starts with:")
            print("   " + content.split('\n')[0][:100])
            
            if 'require' in content or 'include' in content:
                for line in content.split('\n')[:10]:
                    if 'require' in line or 'include' in line:
                        print(f"   Found: {line.strip()}")
            
            os.unlink(tmp_path)
        except Exception as e:
            print(f"   Could not analyze dashboard: {e}")
        
        ftp.quit()
        
        print("\n✅ Connection test created!")
        print("\n🎯 Visit: https://11klassniki.ru/test-connection.php")
        print("\nThis will show:")
        print("• Where database files are located")
        print("• What connection method works")
        print("• How dashboard files connect")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()