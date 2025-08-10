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
    print("🔧 REVERTING CHANGES AND CHECKING DATABASE PROPERLY")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # 1. Create a proper database check
        print("\n1️⃣ Creating comprehensive database check...")
        
        db_check = '''<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

$page_title = 'Database Check';

$greyContent1 = '<div class="container mt-4"><h1>Database Status Check</h1></div>';

$greyContent2 = '<div class="container">';

if ($connection) {
    // Check ALL tables with counts
    $greyContent2 .= '<h2>Table Counts:</h2><table class="table table-bordered">';
    $greyContent2 .= '<tr><th>Table</th><th>Count</th><th>Status</th></tr>';
    
    $tables = ['schools', 'vpo', 'spo', 'posts', 'news', 'users', 'comments'];
    
    foreach ($tables as $table) {
        $result = @mysqli_query($connection, "SELECT COUNT(*) as cnt FROM $table");
        if ($result) {
            $count = mysqli_fetch_assoc($result)['cnt'];
            $status = $count > 0 ? '<span class="badge bg-success">Has Data</span>' : '<span class="badge bg-warning">Empty</span>';
            $greyContent2 .= "<tr><td><strong>$table</strong></td><td>" . number_format($count) . "</td><td>$status</td></tr>";
        } else {
            $greyContent2 .= "<tr><td><strong>$table</strong></td><td>-</td><td><span class='badge bg-danger'>Not Found</span></td></tr>";
        }
    }
    
    $greyContent2 .= '</table>';
    
    // Show sample data from posts
    $greyContent2 .= '<h2>Sample Posts Data:</h2>';
    $result = mysqli_query($connection, "SELECT * FROM posts LIMIT 3");
    if ($result && mysqli_num_rows($result) > 0) {
        $greyContent2 .= '<pre class="bg-light p-3">';
        while ($row = mysqli_fetch_assoc($result)) {
            $greyContent2 .= "ID: " . $row['id'] . "\n";
            foreach ($row as $key => $value) {
                if (strlen($value) > 100) {
                    $greyContent2 .= "$key: " . substr(htmlspecialchars($value), 0, 100) . "...\n";
                } else {
                    $greyContent2 .= "$key: " . htmlspecialchars($value) . "\n";
                }
            }
            $greyContent2 .= "\n---\n\n";
        }
        $greyContent2 .= '</pre>';
    } else {
        $greyContent2 .= '<div class="alert alert-warning">No posts data found</div>';
    }
    
    // Check connection status
    $greyContent2 .= '<h2>Connection Info:</h2>';
    $greyContent2 .= '<ul>';
    $greyContent2 .= '<li>Connection Status: <span class="badge bg-success">Connected</span></li>';
    $greyContent2 .= '<li>Server Info: ' . mysqli_get_server_info($connection) . '</li>';
    $greyContent2 .= '<li>Character Set: ' . mysqli_character_set_name($connection) . '</li>';
    $greyContent2 .= '</ul>';
    
} else {
    $greyContent2 .= '<div class="alert alert-danger">Database connection failed!</div>';
}

$greyContent2 .= '</div>';

$greyContent3 = '';
$greyContent4 = '';
$greyContent5 = '';
$greyContent6 = '';

include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>'''
        
        upload_file(ftp, db_check, 'database-check.php')
        print("   ✅ Created comprehensive database check")
        
        # 2. Revert index.php if needed
        print("\n2️⃣ Checking if index.php needs reverting...")
        
        # Download current index.php
        with tempfile.NamedTemporaryFile(delete=False) as tmp:
            tmp_path = tmp.name
        
        ftp.retrbinary('RETR index.php', open(tmp_path, 'wb').write)
        
        with open(tmp_path, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # Check if it was wrongly changed
        if 'FROM news' in content and 'posts' not in content:
            print("   ⚠️  Index.php was changed, reverting...")
            content = content.replace('FROM news', 'FROM posts')
            content = content.replace('"news"', '"posts"')
            upload_file(ftp, content, 'index.php')
            print("   ✅ Reverted index.php to use posts table")
        else:
            print("   ✅ Index.php is correct")
        
        os.unlink(tmp_path)
        
        ftp.quit()
        
        print("\n✅ Database check created!")
        print("\n🎯 Please visit: https://11klassniki.ru/database-check.php")
        print("\nThis will show:")
        print("• Exact counts from ALL tables")
        print("• Sample data from posts table")
        print("• Connection status")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()