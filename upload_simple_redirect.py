#!/usr/bin/env python3
import ftplib

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.set_pasv(True)
    
    ftp.cwd('/11klassnikiru')
    with open('simple_redirect_test.php', 'rb') as f:
        ftp.storbinary('STOR simple_redirect_test.php', f)
    print("✓ Uploaded simple_redirect_test.php")
    
    ftp.quit()
    print("Test multiple redirects: https://11klassniki.ru/simple_redirect_test.php")
    
except Exception as e:
    print(f"Error: {e}")