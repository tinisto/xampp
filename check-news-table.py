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
    print("🔍 CHECKING NEWS TABLE STRUCTURE")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Create a page to check the actual table structure
        check_page = '''<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

$page_title = 'News Table Check';

$greyContent1 = '<div class="container mt-4"><h1>News Table Structure Check</h1></div>';

$greyContent2 = '<div class="container">';

if ($connection) {
    $greyContent2 .= '<div class="alert alert-success">✅ Database connected!</div>';
    
    // Check both posts and news tables
    $greyContent2 .= '<h2>Checking Tables:</h2>';
    
    // Check posts table
    $greyContent2 .= '<h3>1. POSTS table:</h3>';
    $result = @mysqli_query($connection, "SELECT COUNT(*) as cnt FROM posts");
    if ($result) {
        $count = mysqli_fetch_assoc($result)['cnt'];
        $greyContent2 .= '<p>✅ Posts table exists with <strong>' . $count . '</strong> records</p>';
        
        // Show columns
        $greyContent2 .= '<p>Columns in posts table:</p><ul>';
        $cols = mysqli_query($connection, "SHOW COLUMNS FROM posts");
        while ($col = mysqli_fetch_assoc($cols)) {
            $greyContent2 .= '<li>' . $col['Field'] . ' (' . $col['Type'] . ')</li>';
        }
        $greyContent2 .= '</ul>';
        
        // Show sample data
        if ($count > 0) {
            $greyContent2 .= '<p>Sample post:</p><pre class="bg-light p-3">';
            $sample = mysqli_query($connection, "SELECT * FROM posts LIMIT 1");
            $row = mysqli_fetch_assoc($sample);
            foreach ($row as $key => $value) {
                $greyContent2 .= htmlspecialchars($key) . ': ' . htmlspecialchars(substr($value, 0, 100)) . "\n";
            }
            $greyContent2 .= '</pre>';
        }
    } else {
        $greyContent2 .= '<p>❌ Posts table not found</p>';
    }
    
    // Check news table
    $greyContent2 .= '<h3>2. NEWS table:</h3>';
    $result = @mysqli_query($connection, "SELECT COUNT(*) as cnt FROM news");
    if ($result) {
        $count = mysqli_fetch_assoc($result)['cnt'];
        $greyContent2 .= '<p>✅ News table exists with <strong>' . $count . '</strong> records</p>';
        
        // Show columns
        $greyContent2 .= '<p>Columns in news table:</p><ul>';
        $cols = mysqli_query($connection, "SHOW COLUMNS FROM news");
        while ($col = mysqli_fetch_assoc($cols)) {
            $greyContent2 .= '<li>' . $col['Field'] . ' (' . $col['Type'] . ')</li>';
        }
        $greyContent2 .= '</ul>';
        
        // Show sample data
        if ($count > 0) {
            $greyContent2 .= '<p>Sample news:</p><pre class="bg-light p-3">';
            $sample = mysqli_query($connection, "SELECT * FROM news LIMIT 1");
            $row = mysqli_fetch_assoc($sample);
            foreach ($row as $key => $value) {
                $greyContent2 .= htmlspecialchars($key) . ': ' . htmlspecialchars(substr($value, 0, 100)) . "\n";
            }
            $greyContent2 .= '</pre>';
        }
    } else {
        $greyContent2 .= '<p>❌ News table not found</p>';
    }
    
    // Check what news-simple.php is doing
    $greyContent2 .= '<h3>3. Current news page query:</h3>';
    $greyContent2 .= '<p>The news page is querying: <code>SELECT * FROM posts</code></p>';
    
} else {
    $greyContent2 .= '<div class="alert alert-danger">❌ Database connection failed!</div>';
}

$greyContent2 .= '</div>';

$greyContent3 = '';
$greyContent4 = '';
$greyContent5 = '';
$greyContent6 = '';

include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>'''
        
        upload_file(ftp, check_page, 'check-news-table.php')
        print("   ✅ Created news table check page")
        
        ftp.quit()
        
        print("\n✅ Check page created!")
        print("\n🎯 Visit: https://11klassniki.ru/check-news-table.php")
        print("\nThis will show:")
        print("• Which table has your news data (posts or news)")
        print("• The actual column names")
        print("• Sample data from the table")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()