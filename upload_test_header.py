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
    with open('test_header_issue.php', 'rb') as f:
        ftp.storbinary('STOR test_header_issue.php', f)
    print("✓ Uploaded test_header_issue.php")
    
    ftp.quit()
    print("Check: https://11klassniki.ru/test_header_issue.php")
    
except Exception as e:
    print(f"Error: {e}")