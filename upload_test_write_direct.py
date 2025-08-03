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
    with open('test_write_direct.php', 'rb') as f:
        ftp.storbinary('STOR test_write_direct.php', f)
    print("✓ Uploaded test_write_direct.php")
    
    ftp.quit()
    print("Test: https://11klassniki.ru/test_write_direct.php")
    print("Then try the /write link from there")
    
except Exception as e:
    print(f"Error: {e}")