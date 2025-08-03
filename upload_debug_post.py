#!/usr/bin/env python3
import ftplib

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

print("Uploading debug_post_direct.php...")

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.set_pasv(True)
    
    # Navigate to web root
    ftp.cwd('/11klassnikiru')
    
    # Upload the file
    with open('debug_post_direct.php', 'rb') as f:
        ftp.storbinary('STOR debug_post_direct.php', f)
    print("✓ Uploaded debug_post_direct.php")
    
    ftp.quit()
    print("\nCheck: https://11klassniki.ru/debug_post_direct.php")
    
except Exception as e:
    print(f"Error: {e}")