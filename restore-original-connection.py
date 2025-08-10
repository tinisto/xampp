#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import ftplib
import tempfile
import os

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def main():
    print("🔍 CHECKING WHAT HAPPENED TO THE ORIGINAL CONNECTION")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Check for backup files
        print("\n1️⃣ Looking for backup connection files...")
        
        try:
            ftp.cwd('database')
            files = ftp.nlst()
            
            print("   Files in /database:")
            connection_files = []
            for f in files:
                if 'connection' in f.lower() or 'db' in f.lower():
                    print(f"   - {f}")
                    connection_files.append(f)
            
            # Look for the original that I broke
            if 'db_connections_broken.php' in files:
                print("\n   ✅ Found backup: db_connections_broken.php")
                print("   This was the ORIGINAL working file before I broke it!")
                
                # Download and check it
                with tempfile.NamedTemporaryFile(delete=False) as tmp:
                    tmp_path = tmp.name
                
                ftp.retrbinary('RETR db_connections_broken.php', open(tmp_path, 'wb').write)
                
                with open(tmp_path, 'r', encoding='utf-8') as f:
                    content = f.read()
                
                print("\n   Original file content preview:")
                print("   " + content[:200].replace('\n', '\n   '))
                
                # If it's the env-based one, we need the actual working one
                if 'loadEnv.php' in content:
                    print("\n   ⚠️  This is the env-based connection that redirects to /error")
                    print("   Need to find the ACTUAL working connection...")
                
                os.unlink(tmp_path)
            
            ftp.cwd('..')
        except Exception as e:
            print(f"   Error checking database directory: {e}")
        
        # Check if there's a working config file
        print("\n2️⃣ Checking for config files...")
        
        config_files = ['config.php', 'db_config.php', 'database.php']
        for config in config_files:
            try:
                size = ftp.size(config)
                print(f"   ✅ Found {config} ({size} bytes)")
                
                # Download and check
                with tempfile.NamedTemporaryFile(delete=False) as tmp:
                    tmp_path = tmp.name
                
                ftp.retrbinary(f'RETR {config}', open(tmp_path, 'wb').write)
                
                with open(tmp_path, 'r', encoding='utf-8') as f:
                    content = f.read()
                
                # Look for database credentials
                if 'admin_claude' in content or 'mysqli_connect' in content:
                    print(f"   👀 {config} contains database info!")
                    
                    # Extract host if possible
                    import re
                    host_match = re.search(r"['\"]host['\"].*?['\"]([^'\"]+)['\"]", content, re.IGNORECASE)
                    if host_match:
                        print(f"   Found host: {host_match.group(1)}")
                
                os.unlink(tmp_path)
            except:
                pass
        
        # Create a page to check includes directory
        print("\n3️⃣ Creating includes directory check...")
        
        check_content = '''<?php
$page_title = 'Finding Original Connection';

$greyContent1 = '<div class="container mt-4"><h1>Finding Original Database Connection</h1></div>';

$greyContent2 = '<div class="container">';

// Check includes directory
$greyContent2 .= '<h2>Checking /includes directory:</h2>';
$includes_dir = $_SERVER['DOCUMENT_ROOT'] . '/includes';
if (is_dir($includes_dir)) {
    $greyContent2 .= '<ul>';
    $files = scandir($includes_dir);
    foreach ($files as $file) {
        if (strpos($file, 'db') !== false || strpos($file, 'config') !== false || strpos($file, 'connect') !== false) {
            $greyContent2 .= '<li>' . $file;
            
            // Check if it has connection info
            $content = @file_get_contents($includes_dir . '/' . $file);
            if ($content && strpos($content, 'mysqli_connect') !== false) {
                $greyContent2 .= ' <span class="badge bg-success">Contains mysqli_connect!</span>';
                
                // Try to extract host
                if (preg_match('/mysqli_connect\s*\(\s*[\'"]([^\'"]+)[\'"]/i', $content, $matches)) {
                    $greyContent2 .= '<br>Host: <code>' . htmlspecialchars($matches[1]) . '</code>';
                }
            }
            
            $greyContent2 .= '</li>';
        }
    }
    $greyContent2 .= '</ul>';
}

// Check root directory for config files
$greyContent2 .= '<h2>Checking root directory:</h2>';
$greyContent2 .= '<ul>';
$root_files = ['config.php', 'configuration.php', 'settings.php', 'db_config.php'];
foreach ($root_files as $file) {
    $path = $_SERVER['DOCUMENT_ROOT'] . '/' . $file;
    if (file_exists($path)) {
        $greyContent2 .= '<li>' . $file . ' (exists)';
        
        $content = @file_get_contents($path);
        if ($content && strpos($content, 'admin_claude') !== false) {
            $greyContent2 .= ' <span class="badge bg-success">Contains admin_claude!</span>';
        }
        
        $greyContent2 .= '</li>';
    }
}
$greyContent2 .= '</ul>';

// Check config directory
$greyContent2 .= '<h2>Checking /config directory:</h2>';
$config_dir = $_SERVER['DOCUMENT_ROOT'] . '/config';
if (is_dir($config_dir)) {
    $greyContent2 .= '<ul>';
    $files = scandir($config_dir);
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            $greyContent2 .= '<li>' . $file . '</li>';
        }
    }
    $greyContent2 .= '</ul>';
}

$greyContent2 .= '</div>';

$greyContent3 = '';
$greyContent4 = '';
$greyContent5 = '';
$greyContent6 = '';

include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>'''
        
        with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
            tmp.write(check_content)
            tmp_path = tmp.name
        
        with open(tmp_path, 'rb') as file:
            ftp.storbinary('STOR find-original-connection.php', file)
        os.unlink(tmp_path)
        
        print("   ✅ Created connection finder page")
        
        ftp.quit()
        
        print("\n✅ Search complete!")
        print("\n🎯 Visit: https://11klassniki.ru/find-original-connection.php")
        print("\nThis will help find where the original working connection was")
        
        print("\n⚠️  What happened:")
        print("1. Your site WAS working with a database connection")
        print("2. I found db_connections.php using env variables and 'fixed' it")
        print("3. But I broke the working connection in the process")
        print("4. Now we need to find the ORIGINAL working connection")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()