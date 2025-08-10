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
    print("🔧 FIXING TABLE NAMES TO MATCH DATABASE")
    print("Changing 'posts' to 'news' in all queries")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # 1. Fix homepage to use 'news' table
        print("\n1️⃣ Fixing homepage...")
        
        # Download current index.php
        with tempfile.NamedTemporaryFile(delete=False) as tmp:
            tmp_path = tmp.name
        
        ftp.retrbinary('RETR index.php', open(tmp_path, 'wb').write)
        
        # Read and fix content
        with open(tmp_path, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # Replace posts with news
        content = content.replace('FROM posts', 'FROM news')
        content = content.replace('"posts"', '"news"')
        
        # Upload fixed version
        upload_file(ftp, content, 'index.php')
        os.unlink(tmp_path)
        print("   ✅ Fixed homepage to use 'news' table")
        
        # 2. Fix news page
        print("\n2️⃣ Fixing news page...")
        
        # Download news-simple.php
        try:
            with tempfile.NamedTemporaryFile(delete=False) as tmp:
                tmp_path = tmp.name
            
            ftp.retrbinary('RETR news-simple.php', open(tmp_path, 'wb').write)
            
            with open(tmp_path, 'r', encoding='utf-8') as f:
                content = f.read()
            
            # Replace posts with news
            content = content.replace('FROM posts', 'FROM news')
            content = content.replace('"posts"', '"news"')
            
            # Also need to check column names - news table might use different columns
            # Change title_post to title, text_post to content, etc
            content = content.replace("'title_post'", "'title'")
            content = content.replace("'text_post'", "'content'")
            content = content.replace("'date_post'", "'created_at'")
            
            upload_file(ftp, content, 'news-simple.php')
            os.unlink(tmp_path)
            print("   ✅ Fixed news page to use 'news' table")
        except:
            print("   ⚠️  Could not update news-simple.php")
        
        # 3. Create a quick table structure check
        print("\n3️⃣ Creating table structure check...")
        
        check_page = '''<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

header('Content-Type: text/plain; charset=utf-8');

echo "TABLE STRUCTURE CHECK\n";
echo "===================\n\n";

if ($connection) {
    // Check news table structure
    echo "NEWS table columns:\n";
    $result = mysqli_query($connection, "SHOW COLUMNS FROM news");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
        }
    }
    
    echo "\n\nSample NEWS data:\n";
    $result = mysqli_query($connection, "SELECT * FROM news LIMIT 2");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            print_r($row);
            echo "\n";
        }
    }
    
    // Check if vpo or vuz table
    echo "\n\nVPO/VUZ table check:\n";
    $tables = ['vpo', 'vuz'];
    foreach ($tables as $table) {
        $result = mysqli_query($connection, "SELECT COUNT(*) as cnt FROM $table LIMIT 1");
        if ($result) {
            echo "✓ Table '$table' exists with " . mysqli_fetch_assoc($result)['cnt'] . " records\n";
        } else {
            echo "✗ Table '$table' not found\n";
        }
    }
    
    // Check schools/school table
    echo "\n\nSCHOOLS table check:\n";
    $tables = ['schools', 'school'];
    foreach ($tables as $table) {
        $result = mysqli_query($connection, "SELECT COUNT(*) as cnt FROM $table LIMIT 1");
        if ($result) {
            echo "✓ Table '$table' exists with " . mysqli_fetch_assoc($result)['cnt'] . " records\n";
        } else {
            echo "✗ Table '$table' not found\n";
        }
    }
}
?>'''
        
        upload_file(ftp, check_page, 'check-table-structure.php')
        print("   ✅ Created table structure check")
        
        ftp.quit()
        
        print("\n✅ INITIAL FIXES APPLIED!")
        print("\n🎯 Next step:")
        print("Visit: https://11klassniki.ru/check-table-structure.php")
        print("This will show the actual column names in your tables")
        print("\nOnce we see the correct column names, I can fix all the queries properly!")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()