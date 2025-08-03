#!/usr/bin/env python3
import ftplib

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

print("Uploading check_posts_urls.php...")

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.set_pasv(True)
    
    # Navigate to web root
    ftp.cwd('/11klassnikiru')
    
    # Upload the file
    with open('check_posts_urls.php', 'rb') as f:
        ftp.storbinary('STOR check_posts_urls.php', f)
    print("✓ Uploaded check_posts_urls.php")
    
    ftp.quit()
    print("\nCheck: https://11klassniki.ru/check_posts_urls.php")
    
except Exception as e:
    print(f"Error: {e}")