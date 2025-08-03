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
    with open('verify_final_deployment.php', 'rb') as f:
        ftp.storbinary('STOR verify_final_deployment.php', f)
    print("✓ Uploaded verify_final_deployment.php")
    
    ftp.quit()
    print("Verify deployment: https://11klassniki.ru/verify_final_deployment.php")
    
except Exception as e:
    print(f"Error: {e}")