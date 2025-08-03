#!/usr/bin/env python3
import ftplib

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

print("Uploading htaccess checker...")

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.set_pasv(True)
    
    ftp.cwd('/11klassnikiru')
    
    with open('check_htaccess.php', 'rb') as f:
        ftp.storbinary('STOR check_htaccess.php', f)
    print("✓ Uploaded check_htaccess.php")
    
    # Also upload .htaccess file
    print("\nUploading .htaccess file...")
    with open('.htaccess', 'rb') as f:
        ftp.storbinary('STOR .htaccess', f)
    print("✓ Uploaded .htaccess")
    
    ftp.quit()
    print("\nCheck: https://11klassniki.ru/check_htaccess.php")
    
except Exception as e:
    print(f"Error: {e}")