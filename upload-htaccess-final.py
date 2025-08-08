#!/usr/bin/env python3
"""Upload final htaccess"""

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
    
    with open('.htaccess', 'rb') as f:
        ftp.storbinary('STOR .htaccess', f)
    print("✓ Updated .htaccess uploaded")
    
    ftp.quit()
    
    print("\nAll pages should now work:")
    print("- https://11klassniki.ru/news")
    print("- https://11klassniki.ru/tests") 
    print("- https://11klassniki.ru/spo-all-regions")
    print("- https://11klassniki.ru/vpo-all-regions")
    
except Exception as e:
    print(f"Error: {e}")