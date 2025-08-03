#!/usr/bin/env python3

import ftplib
import os

# FTP Configuration
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

def upload_db_test():
    """Upload database test script"""
    
    print("📤 Uploading database test script...")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd('/11klassnikiru')
        
        # Upload compare_databases.php
        local_file = '/Applications/XAMPP/xamppfiles/htdocs/compare_databases.php'
        if os.path.exists(local_file):
            with open(local_file, 'rb') as f:
                ftp.storbinary('STOR compare_databases.php', f)
            print("✅ fix-redirect.php uploaded!")
        else:
            print("❌ safe-migrate.php not found locally")
        
        ftp.quit()
        print("🔗 Test: https://11klassniki.ru/db-test.php")
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")

if __name__ == "__main__":
    upload_db_test()