#!/usr/bin/env python3
import ftplib

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

print("Uploading simple post pages...")

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.set_pasv(True)
    
    # Navigate to post directory
    ftp.cwd('/11klassnikiru/pages/post')
    
    # Backup current post.php
    try:
        ftp.rename('post.php', 'post-backup.php')
        print("✓ Backed up current post.php")
    except:
        print("Could not backup post.php (might already exist)")
    
    # Upload simple version
    with open('pages/post/post-simple.php', 'rb') as f:
        ftp.storbinary('STOR post-simple.php', f)
    print("✓ Uploaded post-simple.php")
    
    # Upload temp version as main post.php
    with open('pages/post/post-temp.php', 'rb') as f:
        ftp.storbinary('STOR post.php', f)
    print("✓ Uploaded new post.php (using simple version)")
    
    ftp.quit()
    print("\nPost pages should now work with the simplified version")
    
except Exception as e:
    print(f"Error: {e}")