#!/usr/bin/env python3
"""Check what's actually on the server"""

import ftplib
from datetime import datetime

# FTP credentials
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_PATH = "/11klassnikiru/"

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(FTP_PATH)
    
    print("Checking news-new.php on server...")
    
    # Get file info
    files = []
    ftp.retrlines('LIST news*', files.append)
    
    print("\nNews files on server:")
    for f in files:
        print(f)
    
    # Download current news-new.php
    print("\nDownloading current news-new.php...")
    with open('news-new-from-server.php', 'wb') as f:
        try:
            ftp.retrbinary('RETR news-new.php', f.write)
            print("✓ Downloaded news-new.php")
        except:
            print("✗ Could not download news-new.php")
    
    # Check file size
    try:
        size = ftp.size('news-new.php')
        print(f"news-new.php size: {size} bytes")
    except:
        pass
    
    # Let's also check what happens with a direct test
    test_content = """<?php
// Direct test file - {}
echo "<!-- DIRECT TEST FILE -->";
echo "<h1>This is a direct test</h1>";
$greyContent1 = '<div style="background: red; padding: 40px;"><h1>DIRECT TEST WORKING</h1></div>';
$greyContent2 = '';
$greyContent3 = '';
$greyContent4 = '';
$greyContent5 = '<div style="background: yellow; padding: 40px;">If you see this, the file is working!</div>';
$greyContent6 = '';
$blueContent = '';
$pageTitle = 'Direct Test';
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>""".format(datetime.now())
    
    with open('direct-test.php', 'w') as f:
        f.write(test_content)
    
    with open('direct-test.php', 'rb') as f:
        ftp.storbinary('STOR direct-test.php', f)
    print("\n✓ Uploaded direct-test.php")
    
    ftp.quit()
    
    # Show what's in the downloaded file
    print("\nContent of news-new.php from server:")
    print("-" * 50)
    try:
        with open('news-new-from-server.php', 'r') as f:
            content = f.read()
            print(content[:500])  # First 500 chars
            if len(content) > 500:
                print(f"... (total {len(content)} bytes)")
    except:
        print("Could not read file")
    
    print("\nTest these:")
    print("1. https://11klassniki.ru/direct-test.php")
    print("2. https://11klassniki.ru/news-working.php")
    
except Exception as e:
    print(f"Error: {e}")