#!/usr/bin/env python3
"""Upload htaccess fix"""

import ftplib

# FTP credentials
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_PATH = "/11klassnikiru/"

try:
    print("Uploading .htaccess fix...")
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(FTP_PATH)
    
    with open('.htaccess', 'rb') as f:
        ftp.storbinary('STOR .htaccess', f)
    
    print("✓ .htaccess uploaded")
    
    # Also upload diagnostic file
    with open('check-homepage.php', 'rb') as f:
        ftp.storbinary('STOR check-homepage.php', f)
    print("✓ Diagnostic file uploaded")
    
    ftp.quit()
    
    print("\nTest these URLs:")
    print("1. https://11klassniki.ru/ (should show homepage)")
    print("2. https://11klassniki.ru/check-homepage.php (diagnostic)")
    
except Exception as e:
    print(f"Error: {e}")