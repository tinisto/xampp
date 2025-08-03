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
    with open('login_process_debug.php', 'rb') as f:
        ftp.storbinary('STOR login_process_debug.php', f)
    print("✓ Uploaded login_process_debug.php")
    
    ftp.quit()
    print("Debug process uploaded - now create a test form")
    
except Exception as e:
    print(f"Error: {e}")