#!/usr/bin/env python3
import ftplib

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

print("Uploading debug post and replacing current post.php...")

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.set_pasv(True)
    
    # Navigate to post directory
    ftp.cwd('/11klassnikiru/pages/post')
    
    # Backup current post.php
    try:
        ftp.rename('post.php', 'post-original.php')
        print("✓ Backed up post.php to post-original.php")
    except:
        print("Backup already exists or failed")
    
    # Upload debug version as post.php
    with open('pages/post/post-debug-new.php', 'rb') as f:
        ftp.storbinary('STOR post.php', f)
    print("✓ Uploaded debug version as post.php")
    
    # Also upload test_rewrite.php
    ftp.cwd('/11klassnikiru/pages/post')
    with open('test_rewrite.php', 'rb') as f:
        ftp.storbinary('STOR test_rewrite.php', f)
    print("✓ Uploaded test_rewrite.php")
    
    ftp.quit()
    print("\nNow try:")
    print("1. https://11klassniki.ru/post/ledi-v-pogonah (should show debug info)")
    print("2. https://11klassniki.ru/post/test (should show rewrite test)")
    
except Exception as e:
    print(f"Error: {e}")