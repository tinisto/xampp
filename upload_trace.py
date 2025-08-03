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
    with open('trace_redirect_flow.php', 'rb') as f:
        ftp.storbinary('STOR trace_redirect_flow.php', f)
    print("✓ Uploaded trace_redirect_flow.php")
    
    ftp.quit()
    print("Test complete flow: https://11klassniki.ru/trace_redirect_flow.php?redirect=/write")
    
except Exception as e:
    print(f"Error: {e}")