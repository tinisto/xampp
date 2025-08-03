#!/usr/bin/env python3

import ftplib

# FTP Configuration
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

try:
    print("🚀 Uploading debug script...")
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd('/11klassnikiru')
    
    with open('debug_category_error.php', 'rb') as file:
        ftp.storbinary('STOR debug_category_error.php', file)
        print("✅ Uploaded debug script")
    
    ftp.quit()
    print("\n🔍 Visit: https://11klassniki.ru/debug_category_error.php")
    
except Exception as e:
    print(f"❌ Upload failed: {str(e)}")