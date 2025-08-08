#!/usr/bin/env python3
"""Upload diagnostic files"""

import ftplib

# FTP credentials
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_PATH = "/11klassnikiru/"

try:
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(FTP_PATH)
    
    with open('check-default-doc.php', 'rb') as f:
        ftp.storbinary('STOR check-default-doc.php', f)
    
    print("✓ check-default-doc.php uploaded")
    
    ftp.quit()
    
    print("\nPlease check:")
    print("1. https://11klassniki.ru/test-index.php")
    print("2. https://11klassniki.ru/check-default-doc.php")
    
except Exception as e:
    print(f"Error: {e}")