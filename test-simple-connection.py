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
    print("🔍 CREATING SIMPLE CONNECTION TEST")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Create the simplest possible test
        test_page = '''<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Simple Connection Test</h1>";
echo "<pre>";

// Test 1: Direct connection with your credentials
echo "Test 1: Direct mysqli connection\n";
echo "Username: admin_claude\n";
echo "Database: 11klassniki_claude\n\n";

$conn = @mysqli_connect('localhost', 'admin_claude', 'franko85!!@@85', '11klassniki_claude');

if ($conn) {
    echo "✅ CONNECTION SUCCESSFUL!\n\n";
    
    // Quick test
    $result = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM posts");
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        echo "Posts count: " . $row['cnt'] . "\n";
    }
    
    $result = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM news");
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        echo "News count: " . $row['cnt'] . "\n";
    }
    
    mysqli_close($conn);
} else {
    echo "❌ CONNECTION FAILED\n";
    echo "Error: " . mysqli_connect_error() . "\n\n";
    
    echo "Possible issues:\n";
    echo "1. Password might be incorrect\n";
    echo "2. Username might be incorrect\n";
    echo "3. Database host might not be 'localhost'\n";
}

// Test 2: Check existing db_connections.php
echo "\n\nTest 2: Check db_connections.php\n";
echo "=====================================\n";

if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php')) {
    echo "File exists\n";
    
    // Include it
    @include $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
    
    if (isset($connection) && $connection) {
        echo "✅ \$connection variable is set and true\n";
    } else {
        echo "❌ \$connection variable is not set or false\n";
    }
} else {
    echo "❌ db_connections.php not found\n";
}

// Test 3: Look for working files
echo "\n\nTest 3: Check working dashboard files\n";
echo "=====================================\n";

$dashFile = $_SERVER['DOCUMENT_ROOT'] . '/dashboard-news-functional.php';
if (file_exists($dashFile)) {
    echo "Dashboard file exists\n";
    
    // Check how it connects
    $content = file_get_contents($dashFile);
    if (preg_match('/mysqli_connect\s*\([^)]+\)/', $content, $matches)) {
        echo "Dashboard uses: " . $matches[0] . "\n";
    }
}

echo "</pre>";

// Try to find any config files
echo "<h2>Config Files Check</h2><pre>";

$configs = glob($_SERVER['DOCUMENT_ROOT'] . '/*.php');
foreach ($configs as $config) {
    $name = basename($config);
    if (strpos($name, 'config') !== false || strpos($name, 'db') !== false) {
        echo "Found: $name\n";
    }
}

echo "</pre>";
?>'''
        
        upload_file(ftp, test_page, 'test-simple.php')
        print("   ✅ Created simple test page")
        
        ftp.quit()
        
        print("\n✅ Simple test created!")
        print("\n🎯 Visit: https://11klassniki.ru/test-simple.php")
        print("\nThis will show if the connection credentials are correct")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()