#!/usr/bin/env python3
import ftplib

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

print("Uploading comparison script...")

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.set_pasv(True)
    
    ftp.cwd('/11klassnikiru')
    
    with open('compare_working_posts.php', 'rb') as f:
        ftp.storbinary('STOR compare_working_posts.php', f)
    print("✓ Uploaded compare_working_posts.php")
    
    ftp.quit()
    print("\nCheck: https://11klassniki.ru/compare_working_posts.php")
    
except Exception as e:
    print(f"Error: {e}")