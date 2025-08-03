#!/usr/bin/env python3
import ftplib

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

print("Uploading full test script...")

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.set_pasv(True)
    
    ftp.cwd('/11klassnikiru')
    
    with open('test_post_full.php', 'rb') as f:
        ftp.storbinary('STOR test_post_full.php', f)
    print("✓ Uploaded test_post_full.php")
    
    ftp.quit()
    print("\nCheck: https://11klassniki.ru/test_post_full.php")
    
except Exception as e:
    print(f"Error: {e}")