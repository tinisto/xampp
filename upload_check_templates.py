#!/usr/bin/env python3
import ftplib

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

print("Uploading template checker...")

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.set_pasv(True)
    
    ftp.cwd('/11klassnikiru')
    
    with open('check_template_files.php', 'rb') as f:
        ftp.storbinary('STOR check_template_files.php', f)
    print("✓ Uploaded check_template_files.php")
    
    ftp.quit()
    print("\nCheck: https://11klassniki.ru/check_template_files.php")
    
except Exception as e:
    print(f"Error: {e}")