#!/usr/bin/env python3

import ftplib
import os

# FTP Configuration
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

def upload_fix_missing():
    """Upload fix missing data script"""
    
    print("📤 Uploading fix missing data script...")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd('/11klassnikiru')
        
        # Upload fix_missing_data.php
        local_file = '/Applications/XAMPP/xamppfiles/htdocs/fix_missing_data.php'
        if os.path.exists(local_file):
            with open(local_file, 'rb') as f:
                ftp.storbinary('STOR fix_missing_data.php', f)
            print("✅ fix_missing_data.php uploaded!")
        else:
            print("❌ fix_missing_data.php not found locally")
        
        ftp.quit()
        print("\n🔗 Fix missing data at: https://11klassniki.ru/fix_missing_data.php")
        print("\nRecommended steps:")
        print("1. First copy missing Areas & Towns")
        print("2. Then check missing Universities & Colleges")
        print("3. Finally run full copy if needed")
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")

if __name__ == "__main__":
    upload_fix_missing()