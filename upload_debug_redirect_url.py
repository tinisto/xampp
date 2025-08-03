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
    with open('debug_login_redirect_url.php', 'rb') as f:
        ftp.storbinary('STOR debug_login_redirect_url.php', f)
    print("✓ Uploaded debug_login_redirect_url.php")
    
    ftp.quit()
    print("Test: https://11klassniki.ru/debug_login_redirect_url.php?redirect=/test_header_issue.php")
    
except Exception as e:
    print(f"Error: {e}")