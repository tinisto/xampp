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
    print("🔍 FINDING WORKING DATABASE CONNECTION")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Check one of the existing db test files
        print("\n1️⃣ Checking existing db test files...")
        
        test_files = ['test_db_connection.php', 'direct_db_test.php', 'check_claude_db_status.php']
        
        for test_file in test_files:
            try:
                with tempfile.NamedTemporaryFile(delete=False) as tmp:
                    tmp_path = tmp.name
                
                ftp.retrbinary(f'RETR {test_file}', open(tmp_path, 'wb').write)
                
                with open(tmp_path, 'r', encoding='utf-8', errors='ignore') as f:
                    content = f.read()
                
                # Look for connection strings
                if 'mysqli_connect' in content or 'mysql:host=' in content:
                    print(f"\n   Found in {test_file}:")
                    
                    # Extract host info
                    import re
                    
                    # Look for mysqli_connect
                    matches = re.findall(r"mysqli_connect\s*\(\s*['\"]([^'\"]+)['\"]", content)
                    if matches:
                        print(f"   Host: {matches[0]}")
                    
                    # Look for PDO style
                    matches = re.findall(r"mysql:host=([^;]+);", content)
                    if matches:
                        print(f"   PDO Host: {matches[0]}")
                    
                    # Look for specific host strings
                    if 'sqlc2.megasqlservers.com' in content:
                        print("   ✅ Found: sqlc2.megasqlservers.com")
                    if 'ipagemysql.com' in content:
                        print("   ✅ Found: ipagemysql.com")
                    
                os.unlink(tmp_path)
            except:
                pass
        
        # Create a page that tries common iPage hosts
        print("\n2️⃣ Creating iPage connection test...")
        
        test_content = '''<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>iPage Database Host Test</h1>";
echo "<pre>";

// Common iPage database hosts
$hosts = [
    'sqlc2.megasqlservers.com',
    'mysql.ipage.com',
    'ipagemysql.com',
    'db.ipage.com',
    '127.0.0.1'
];

$user = 'admin_claude';
$pass = 'franko85!!@@85';
$db = '11klassniki_claude';

foreach ($hosts as $host) {
    echo "Testing host: $host\n";
    
    $conn = @mysqli_connect($host, $user, $pass, $db);
    
    if ($conn) {
        echo "✅ SUCCESS! Connected to: $host\n\n";
        
        // Test query
        $result = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM posts");
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            echo "Posts count: " . $row['cnt'] . "\n";
        }
        
        // Update db_connections.php with working host
        $connection_content = "<?php\n";
        $connection_content .= "// Working database connection\n";
        $connection_content .= "\$connection = @mysqli_connect('$host', '$user', '$pass', '$db');\n";
        $connection_content .= "if (\$connection) {\n";
        $connection_content .= "    mysqli_set_charset(\$connection, 'utf8mb4');\n";
        $connection_content .= "}\n";
        $connection_content .= "?>";
        
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/database/db_connections_working.php', $connection_content);
        echo "\nCreated db_connections_working.php with correct host!\n";
        
        mysqli_close($conn);
        break;
    } else {
        echo "❌ Failed: " . mysqli_connect_error() . "\n";
    }
    echo "---\n";
}

echo "</pre>";
?>'''
        
        with tempfile.NamedTemporaryFile(mode='w', delete=False, encoding='utf-8') as tmp:
            tmp.write(test_content)
            tmp_path = tmp.name
        
        with open(tmp_path, 'rb') as file:
            ftp.storbinary('STOR find-host.php', file)
        os.unlink(tmp_path)
        
        print("   ✅ Created host finder page")
        
        ftp.quit()
        
        print("\n✅ Host finder created!")
        print("\n🎯 Visit: https://11klassniki.ru/find-host.php")
        print("\nThis will test common iPage database hosts and find the working one")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()