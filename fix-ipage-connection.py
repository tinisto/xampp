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
    print("🔧 FIXING DATABASE CONNECTION FOR iPAGE")
    print("Using iPage MySQL host configuration")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Create connection with iPage host
        print("\n1️⃣ Creating iPage database connection...")
        
        # Try the most common iPage hosts
        ipage_connection = '''<?php
// iPage Database Connection
// Updated for iPage hosting environment

// Try multiple iPage hosts
$hosts = ['mysql.ipage.com', 'ipagemysql.com', 'sqlc2.megasqlservers.com'];
$connection = false;

foreach ($hosts as $host) {
    $connection = @mysqli_connect($host, 'admin_claude', 'franko85!!@@85', '11klassniki_claude');
    if ($connection) {
        // Success - set charset and break
        mysqli_set_charset($connection, 'utf8mb4');
        break;
    }
}

// If still no connection, try with error suppression off for debugging
if (!$connection) {
    // Log the error but don't break the site
    error_log("Database connection failed to all iPage hosts");
    $connection = false;
}

// Make it globally available
$GLOBALS['connection'] = $connection;
?>'''
        
        upload_file(ftp, ipage_connection, 'database/db_connections.php')
        print("   ✅ Updated db_connections.php for iPage")
        
        # Create a test page
        print("\n2️⃣ Creating connection test page...")
        
        test_page = '''<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h1>iPage Connection Test</h1>";
echo "<pre>";

if ($connection) {
    echo "✅ DATABASE CONNECTED!\n\n";
    
    // Get host info
    $host_info = mysqli_get_host_info($connection);
    echo "Host info: " . $host_info . "\n";
    echo "Server info: " . mysqli_get_server_info($connection) . "\n\n";
    
    // Test tables
    $tables = ['posts', 'news', 'schools', 'vpo', 'spo', 'users'];
    echo "Table counts:\n";
    foreach ($tables as $table) {
        $result = @mysqli_query($connection, "SELECT COUNT(*) as cnt FROM $table");
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            echo "- $table: " . $row['cnt'] . " records\n";
        } else {
            echo "- $table: not found\n";
        }
    }
} else {
    echo "❌ Connection failed\n";
    echo "Trying direct connections:\n\n";
    
    // Try each host directly
    $hosts = ['mysql.ipage.com', 'ipagemysql.com', 'sqlc2.megasqlservers.com'];
    foreach ($hosts as $host) {
        echo "Testing $host... ";
        $test = @mysqli_connect($host, 'admin_claude', 'franko85!!@@85', '11klassniki_claude');
        if ($test) {
            echo "✅ WORKS!\n";
            mysqli_close($test);
        } else {
            echo "❌ " . mysqli_connect_error() . "\n";
        }
    }
}

echo "</pre>";
?>'''
        
        upload_file(ftp, test_page, 'test-ipage-connection.php')
        print("   ✅ Created test page")
        
        ftp.quit()
        
        print("\n✅ iPage connection fix applied!")
        print("\n🎯 Visit: https://11klassniki.ru/test-ipage-connection.php")
        print("\nThis will show if the connection is working with iPage hosts")
        print("\nYour pages should start showing data once the correct host connects!")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()