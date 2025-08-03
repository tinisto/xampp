#!/usr/bin/env python3
import ftplib

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

print("Uploading debug script...")

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.set_pasv(True)
    
    ftp.cwd('/11klassnikiru')
    
    with open('debug_specific_posts.php', 'rb') as f:
        ftp.storbinary('STOR debug_specific_posts.php', f)
    print("✓ Uploaded debug_specific_posts.php")
    
    ftp.quit()
    print("\nCheck: https://11klassniki.ru/debug_specific_posts.php")
    
except Exception as e:
    print(f"Error: {e}")