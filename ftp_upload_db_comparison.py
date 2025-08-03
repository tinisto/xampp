#!/usr/bin/env python3

import ftplib
import os

# FTP Configuration
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

def upload_db_comparison():
    """Upload final database comparison script"""
    
    print("📤 Uploading final database comparison script...")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd('/11klassnikiru')
        
        # Upload final_db_comparison.php
        local_file = '/Applications/XAMPP/xamppfiles/htdocs/final_db_comparison.php'
        if os.path.exists(local_file):
            with open(local_file, 'rb') as f:
                ftp.storbinary('STOR final_db_comparison.php', f)
            print("✅ final_db_comparison.php uploaded!")
        else:
            print("❌ final_db_comparison.php not found locally")
        
        # Also upload copy_missing_data.php in case we need it
        local_file2 = '/Applications/XAMPP/xamppfiles/htdocs/copy_missing_data.php'
        if os.path.exists(local_file2):
            with open(local_file2, 'rb') as f:
                ftp.storbinary('STOR copy_missing_data.php', f)
            print("✅ copy_missing_data.php uploaded!")
        
        ftp.quit()
        print("\n🔗 Check results at: https://11klassniki.ru/final_db_comparison.php")
        print("🔗 If needed, copy missing data: https://11klassniki.ru/copy_missing_data.php")
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")

if __name__ == "__main__":
    upload_db_comparison()