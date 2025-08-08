#!/usr/bin/env python3
"""Upload test index"""

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
    
    with open('test-index.php', 'rb') as f:
        ftp.storbinary('STOR test-index.php', f)
    
    print("✓ test-index.php uploaded")
    
    ftp.quit()
    
    print("\nTest: https://11klassniki.ru/test-index.php")
    
except Exception as e:
    print(f"Error: {e}")