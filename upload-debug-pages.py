#!/usr/bin/env python3
"""Upload debug pages"""

import ftplib

# FTP credentials
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_PATH = "/11klassnikiru/"

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(FTP_PATH)
    
    # Upload debug news
    with open('debug-news.php', 'rb') as f:
        ftp.storbinary('STOR debug-news.php', f)
    print("✓ Uploaded debug-news.php")
    
    # Create a test to see if variables are passed
    test_content = """<?php
echo "<!-- VARIABLE TEST -->";
$testVar = "TEST123";
echo "<!-- Before include: testVar = $testVar -->";
$greyContent1 = '<h1>THIS IS A TEST</h1>';
include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
?>"""
    
    with open('test-vars.php', 'w') as f:
        f.write(test_content)
    
    with open('test-vars.php', 'rb') as f:
        ftp.storbinary('STOR test-vars.php', f)
    print("✓ Uploaded test-vars.php")
    
    ftp.quit()
    
    print("\nTest these:")
    print("1. https://11klassniki.ru/debug-news.php")
    print("2. https://11klassniki.ru/test-vars.php")
    
except Exception as e:
    print(f"Error: {e}")