#!/usr/bin/env python3
import ftplib

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

print("Uploading fixed post-content.php...")

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.set_pasv(True)
    
    # Navigate to post directory
    ftp.cwd('/11klassnikiru/pages/post')
    
    # Backup current post-content.php
    try:
        ftp.rename('post-content.php', 'post-content-backup.php')
        print("✓ Backed up current post-content.php")
    except:
        print("Backup already exists or failed")
    
    # Upload fixed version
    with open('pages/post/post-content-fixed.php', 'rb') as f:
        ftp.storbinary('STOR post-content.php', f)
    print("✓ Uploaded fixed post-content.php")
    
    ftp.quit()
    print("\nFixed! The issue was HTML entities in the post content.")
    print("All posts should now display correctly.")
    
except Exception as e:
    print(f"Error: {e}")