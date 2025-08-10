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
    print("🔍 CHECKING DATABASE TABLE NAMES")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Create a test page to check database tables
        test_page = '''<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

header('Content-Type: text/plain; charset=utf-8');

echo "DATABASE TABLE CHECK\n";
echo "===================\n\n";

if ($connection) {
    // Check what tables exist
    $tables_query = "SHOW TABLES";
    $result = $connection->query($tables_query);
    
    echo "Available tables:\n";
    if ($result) {
        while ($row = $result->fetch_row()) {
            echo "- " . $row[0] . "\n";
        }
    }
    
    echo "\n\nChecking specific tables:\n";
    
    // Check VPO table
    $vpo_tables = ['vpo', 'vuz', 'vuz_new', 'universities', 'higher_education'];
    foreach ($vpo_tables as $table) {
        $check = $connection->query("SELECT COUNT(*) as cnt FROM " . $table . " LIMIT 1");
        if ($check) {
            $row = $check->fetch_assoc();
            echo "✓ Table '$table' exists with " . $row['cnt'] . " records\n";
        } else {
            echo "✗ Table '$table' not found\n";
        }
    }
    
    echo "\n";
    
    // Check posts table
    $posts_tables = ['posts', 'news', 'articles', 'blog'];
    foreach ($posts_tables as $table) {
        $check = $connection->query("SELECT COUNT(*) as cnt FROM " . $table . " LIMIT 1");
        if ($check) {
            $row = $check->fetch_assoc();
            echo "✓ Table '$table' exists with " . $row['cnt'] . " records\n";
        } else {
            echo "✗ Table '$table' not found\n";
        }
    }
    
    // Check VUZ table structure if it exists
    echo "\n\nChecking VUZ table structure:\n";
    $desc = $connection->query("DESCRIBE vuz");
    if ($desc) {
        echo "VUZ table columns:\n";
        while ($row = $desc->fetch_assoc()) {
            echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
        }
    }
    
} else {
    echo "Database connection failed\n";
}
?>'''
        
        upload_file(ftp, test_page, 'check-tables.php')
        print("   ✅ Created database check page")
        
        ftp.quit()
        
        print("\n✅ Database check page created!")
        print("\n🎯 Visit: https://11klassniki.ru/check-tables.php")
        print("This will show:")
        print("• All available tables")
        print("• Record counts")
        print("• Table structure")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()