#!/usr/bin/env python3
import ftplib

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

print("Uploading 404 fix...")

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.set_pasv(True)
    
    # Navigate to 404 directory
    ftp.cwd('/11klassnikiru/pages/404')
    
    # Backup current 404.php
    try:
        ftp.rename('404.php', '404-with-construction.php')
        print("✓ Backed up current 404.php")
    except:
        print("Backup already exists or failed")
    
    # Upload simple 404 as main
    with open('pages/404/404-simple.php', 'rb') as f:
        ftp.storbinary('STOR 404.php', f)
    print("✓ Uploaded simple 404.php")
    
    # Also upload test page
    ftp.cwd('/11klassnikiru')
    with open('test_all_main_posts.php', 'rb') as f:
        ftp.storbinary('STOR test_all_main_posts.php', f)
    print("✓ Uploaded test_all_main_posts.php")
    
    ftp.quit()
    print("\nFixed! Non-existent posts will now show a 404 page instead of redirecting to /error")
    print("\nTest all posts at: https://11klassniki.ru/test_all_main_posts.php")
    
except Exception as e:
    print(f"Error: {e}")