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
    print("🔧 TESTING SPECIFIC iPAGE HOSTS")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Create a test page using template system
        print("\n1️⃣ Creating host test page with template...")
        
        test_page = '''<?php
$page_title = 'Testing Database Hosts';

$greyContent1 = '<div class="container mt-4"><h1>Testing iPage Database Hosts</h1></div>';

$greyContent2 = '<div class="container">';

// Test specific hosts for 11klassniki.ru
$hosts = [
    '11klassniki.ru.ipagemysql.com',
    'mysql.11klassniki.ru',
    '11klassniki.ru.db.ipage.com',
    'localhost',
    '127.0.0.1',
    '11klassnikiru.ipagemysql.com', // Without dots
    'ipagemysql.com',
    '192.185.151.148' // Common iPage MySQL IP
];

$user = 'admin_claude';
$pass = 'franko85!!@@85';
$db = '11klassniki_claude';

$greyContent2 .= '<h2>Testing each host:</h2>';
$greyContent2 .= '<table class="table table-bordered">';
$greyContent2 .= '<tr><th>Host</th><th>Result</th><th>Error</th></tr>';

$working_host = null;

foreach ($hosts as $host) {
    $greyContent2 .= '<tr>';
    $greyContent2 .= '<td><code>' . $host . '</code></td>';
    
    $conn = @mysqli_connect($host, $user, $pass, $db);
    
    if ($conn) {
        $greyContent2 .= '<td><span class="badge bg-success">✅ SUCCESS!</span></td>';
        $greyContent2 .= '<td>Connected successfully</td>';
        
        if (!$working_host) {
            $working_host = $host;
            
            // Test a query
            $result = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM posts");
            if ($result) {
                $row = mysqli_fetch_assoc($result);
                $greyContent2 .= '</tr><tr><td colspan="3" class="bg-light">Posts count: ' . $row['cnt'] . '</td>';
            }
        }
        
        mysqli_close($conn);
    } else {
        $error = mysqli_connect_error();
        $greyContent2 .= '<td><span class="badge bg-danger">❌ Failed</span></td>';
        $greyContent2 .= '<td><small>' . htmlspecialchars($error) . '</small></td>';
    }
    
    $greyContent2 .= '</tr>';
}

$greyContent2 .= '</table>';

if ($working_host) {
    $greyContent2 .= '<div class="alert alert-success mt-4">';
    $greyContent2 .= '<h3>✅ Found working host: <code>' . $working_host . '</code></h3>';
    $greyContent2 .= '<p>Updating database connection...</p>';
    $greyContent2 .= '</div>';
    
    // Create the working connection file
    $connection_content = "<?php\n";
    $connection_content .= "// Working iPage database connection\n";
    $connection_content .= "// Host: $working_host\n\n";
    $connection_content .= "\$connection = @mysqli_connect('$working_host', '$user', '$pass', '$db');\n\n";
    $connection_content .= "if (\$connection) {\n";
    $connection_content .= "    mysqli_set_charset(\$connection, 'utf8mb4');\n";
    $connection_content .= "} else {\n";
    $connection_content .= "    error_log('Database connection failed: ' . mysqli_connect_error());\n";
    $connection_content .= "    \$connection = false;\n";
    $connection_content .= "}\n\n";
    $connection_content .= "\$GLOBALS['connection'] = \$connection;\n";
    $connection_content .= "?>";
    
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/database/db_connections_working.php', $connection_content);
    
    $greyContent2 .= '<p>Created: <code>/database/db_connections_working.php</code></p>';
} else {
    $greyContent2 .= '<div class="alert alert-warning mt-4">';
    $greyContent2 .= '<h3>⚠️ No working host found</h3>';
    $greyContent2 .= '<p>Please check your iPage control panel for the correct MySQL hostname.</p>';
    $greyContent2 .= '</div>';
}

$greyContent2 .= '</div>';

$greyContent3 = '';
$greyContent4 = '';
$greyContent5 = '';
$greyContent6 = '';

include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>'''
        
        upload_file(ftp, test_page, 'test-hosts.php')
        print("   ✅ Created host test page")
        
        ftp.quit()
        
        print("\n✅ Host test page created!")
        print("\n🎯 Visit: https://11klassniki.ru/test-hosts.php")
        print("\nThis will test each possible host and show which one works")
        print("If it finds a working host, it will create a connection file automatically")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()