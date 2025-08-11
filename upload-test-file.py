#!/usr/bin/env python3
"""Upload test file to check server"""

import ftplib

# FTP credentials
FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
FTP_DIR = '11klassnikiru'

try:
    ftp = ftplib.FTP(FTP_HOST, timeout=30)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(FTP_DIR)
    
    # Upload test file
    with open('test-server.php', 'rb') as f:
        ftp.storbinary('STOR test-server.php', f)
    
    print("✅ Test file uploaded!")
    print("🌐 Check: https://11klassniki.ru/test-server.php")
    
    ftp.quit()
    
except Exception as e:
    print(f"❌ Error: {e}")