#!/usr/bin/env python3
import ftplib

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

print("Uploading working post.php...")

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.set_pasv(True)
    
    # Navigate to post directory
    ftp.cwd('/11klassnikiru/pages/post')
    
    # Upload working version as post.php
    with open('pages/post/post-working.php', 'rb') as f:
        ftp.storbinary('STOR post.php', f)
    print("✓ Uploaded working version as post.php")
    
    ftp.quit()
    print("\nPost pages should now work properly!")
    print("Try: https://11klassniki.ru/post/ledi-v-pogonah")
    
except Exception as e:
    print(f"Error: {e}")