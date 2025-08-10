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
    print("🔧 CREATING DIRECT DATABASE CONNECTION")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # 1. Create a simple direct connection file
        print("\n1️⃣ Creating direct connection file...")
        
        direct_connection = '''<?php
// Direct database connection for 11klassniki.ru
// This bypasses the .env file issues

// Database credentials
$db_host = 'localhost';
$db_user = '11klassniki_claude';  
$db_pass = 'your_password_here';  // You'll need to update this
$db_name = '11klassniki_claude';

// Create connection
$connection = @mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Check connection
if (!$connection) {
    // For debugging only - remove in production
    error_log("Database connection failed: " . mysqli_connect_error());
    $connection = false;
} else {
    // Set charset
    mysqli_set_charset($connection, 'utf8mb4');
}

// Make connection available globally
$GLOBALS['connection'] = $connection;
?>'''
        
        upload_file(ftp, direct_connection, 'database/db_connection_direct.php')
        print("   ✅ Created direct connection file")
        
        # 2. Create a test page using direct connection
        print("\n2️⃣ Creating test page with direct connection...")
        
        test_page = '''<?php
// Test with direct connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connection_direct.php';

$page_title = 'Direct Connection Test';

$greyContent1 = '<div class="container mt-4"><h1>Direct Database Connection Test</h1></div>';

$greyContent2 = '<div class="container">';

if ($connection) {
    $greyContent2 .= '<div class="alert alert-success">✅ Database connected successfully!</div>';
    
    // Test tables
    $greyContent2 .= '<h2>Table Counts:</h2><table class="table">';
    
    $tables = ['schools', 'vpo', 'spo', 'posts', 'news', 'users'];
    foreach ($tables as $table) {
        $result = @mysqli_query($connection, "SELECT COUNT(*) as cnt FROM $table");
        if ($result) {
            $count = mysqli_fetch_assoc($result)['cnt'];
            $greyContent2 .= "<tr><td>$table</td><td>" . number_format($count) . "</td></tr>";
        }
    }
    
    $greyContent2 .= '</table>';
} else {
    $greyContent2 .= '<div class="alert alert-danger">❌ Database connection failed!</div>';
    $greyContent2 .= '<p>The direct connection file needs the correct database password.</p>';
    $greyContent2 .= '<p>Please update /database/db_connection_direct.php with your database credentials.</p>';
}

$greyContent2 .= '</div>';

$greyContent3 = '';
$greyContent4 = '';
$greyContent5 = '';
$greyContent6 = '';

include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>'''
        
        upload_file(ftp, test_page, 'test-direct-connection.php')
        print("   ✅ Created test page")
        
        # 3. Look for existing working connection
        print("\n3️⃣ Checking for existing database config...")
        
        # Try to find database credentials in existing files
        try:
            # Check if there's a config.php or similar
            files_to_check = ['config.php', 'wp-config.php', 'configuration.php', 'settings.php']
            
            for file in files_to_check:
                try:
                    with tempfile.NamedTemporaryFile(delete=False) as tmp:
                        tmp_path = tmp.name
                    
                    ftp.retrbinary(f'RETR {file}', open(tmp_path, 'wb').write)
                    print(f"   ✓ Found {file}")
                    
                    os.unlink(tmp_path)
                except:
                    pass
        except:
            pass
        
        ftp.quit()
        
        print("\n✅ Direct connection files created!")
        print("\n⚠️  IMPORTANT: The connection needs the correct password!")
        print("\n🎯 Next steps:")
        print("1. Update /database/db_connection_direct.php with your database password")
        print("2. Visit: https://11klassniki.ru/test-direct-connection.php")
        print("\nThe database user appears to be: 11klassniki_claude")
        print("The database name appears to be: 11klassniki_claude")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()