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
    
    # Upload simple test
    with open('simple_test.php', 'rb') as f:
        ftp.storbinary('STOR simple_test.php', f)
    print("✓ Uploaded simple_test.php")
    
    # Upload simple logout
    with open('simple_logout.php', 'rb') as f:
        ftp.storbinary('STOR simple_logout.php', f)
    print("✓ Uploaded simple_logout.php")
    
    ftp.quit()
    print("Test: https://11klassniki.ru/simple_test.php")
    print("Logout: https://11klassniki.ru/simple_logout.php")
    
except Exception as e:
    print(f"Error: {e}")