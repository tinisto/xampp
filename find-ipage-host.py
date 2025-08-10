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
    print("🔧 FINDING CORRECT iPAGE DATABASE HOST")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Look for the host in existing working files
        print("\n1️⃣ Checking dashboard files for working connection...")
        
        # Download a dashboard file that was working
        try:
            with tempfile.NamedTemporaryFile(delete=False) as tmp:
                tmp_path = tmp.name
            
            # Try dashboard file
            ftp.retrbinary('RETR dashboard-news-functional.php', open(tmp_path, 'wb').write)
            
            with open(tmp_path, 'r', encoding='utf-8', errors='ignore') as f:
                content = f.read()
            
            # Look for connection info
            if 'mysqli_connect' in content:
                print("   Found mysqli_connect in dashboard")
                # Extract the line
                for line in content.split('\n'):
                    if 'mysqli_connect' in line and not line.strip().startswith('//'):
                        print(f"   Connection: {line.strip()[:100]}")
            
            os.unlink(tmp_path)
        except:
            print("   Could not check dashboard file")
        
        # Create a page that shows phpinfo to find MySQL host
        print("\n2️⃣ Creating phpinfo check...")
        
        phpinfo_content = '''<?php
// Check PHP configuration for MySQL host
$page_title = 'MySQL Host Check';

$greyContent1 = '<div class="container mt-4"><h1>Finding MySQL Host</h1></div>';

$greyContent2 = '<div class="container">';

// Check environment variables
$greyContent2 .= '<h2>Environment Check:</h2>';
$greyContent2 .= '<pre>';

// Common environment variables for database host
$vars = ['DB_HOST', 'MYSQL_HOST', 'DATABASE_HOST', 'IPAGE_MYSQL_HOST'];
foreach ($vars as $var) {
    if (getenv($var)) {
        $greyContent2 .= "$var: " . getenv($var) . "\n";
    }
}

// Check phpinfo for mysql settings
ob_start();
phpinfo(INFO_MODULES);
$info = ob_get_clean();

if (preg_match('/mysql\.default_host<\/td><td[^>]*>([^<]+)/', $info, $matches)) {
    $greyContent2 .= "\nMySQL default host from phpinfo: " . $matches[1] . "\n";
}

$greyContent2 .= '</pre>';

// Try common iPage patterns
$greyContent2 .= '<h2>Testing iPage host patterns:</h2>';
$greyContent2 .= '<pre>';

// For iPage, the host is often the server name
$server_name = $_SERVER['SERVER_NAME'] ?? '';
$possible_hosts = [
    str_replace('www.', '', $server_name) . '.ipagemysql.com',
    'mysql.' . str_replace('www.', '', $server_name),
    $server_name . '.db.ipage.com',
    'localhost', // Sometimes works on iPage
    '127.0.0.1',
    $_SERVER['SERVER_ADDR'] ?? '' // Server IP
];

$greyContent2 .= "Possible hosts based on server name:\n";
foreach ($possible_hosts as $host) {
    if ($host) {
        $greyContent2 .= "- $host\n";
    }
}

$greyContent2 .= '</pre>';

// Manual instruction
$greyContent2 .= '<div class="alert alert-info mt-4">';
$greyContent2 .= '<h3>To find your database host:</h3>';
$greyContent2 .= '<ol>';
$greyContent2 .= '<li>Log into your iPage control panel</li>';
$greyContent2 .= '<li>Go to MySQL Databases section</li>';
$greyContent2 .= '<li>Look for "Server" or "Hostname" - it might be shown as:<ul>';
$greyContent2 .= '<li>A URL like: yourdomain.ipagemysql.com</li>';
$greyContent2 .= '<li>An IP address like: 192.168.1.1</li>';
$greyContent2 .= '<li>Sometimes just: localhost</li>';
$greyContent2 .= '</ul></li>';
$greyContent2 .= '<li>Or check the phpMyAdmin URL - the host is often in the URL</li>';
$greyContent2 .= '</ol>';
$greyContent2 .= '</div>';

$greyContent2 .= '</div>';

$greyContent3 = '';
$greyContent4 = '';
$greyContent5 = '';
$greyContent6 = '';

include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>'''
        
        upload_file(ftp, phpinfo_content, 'find-mysql-host.php')
        print("   ✅ Created MySQL host finder")
        
        # Also create a simpler direct connection with localhost
        print("\n3️⃣ Testing localhost connection...")
        
        localhost_connection = '''<?php
// Try localhost - sometimes works on iPage
$connection = @mysqli_connect('localhost', 'admin_claude', 'franko85!!@@85', '11klassniki_claude');

if (!$connection) {
    // Try 127.0.0.1
    $connection = @mysqli_connect('127.0.0.1', 'admin_claude', 'franko85!!@@85', '11klassniki_claude');
}

if ($connection) {
    mysqli_set_charset($connection, 'utf8mb4');
} else {
    $connection = false;
}

$GLOBALS['connection'] = $connection;
?>'''
        
        upload_file(ftp, localhost_connection, 'database/db_connections_localhost.php')
        print("   ✅ Created localhost connection test")
        
        ftp.quit()
        
        print("\n✅ Host finder created!")
        print("\n🎯 Visit: https://11klassniki.ru/find-mysql-host.php")
        print("\nThis uses the template system and will help find the MySQL host")
        
        print("\n📌 For iPage hosting:")
        print("• The host is often NOT 'localhost'")
        print("• It's usually something like: yourdomain.ipagemysql.com")
        print("• Or an IP address")
        print("• Check your iPage control panel MySQL section for the exact host")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()