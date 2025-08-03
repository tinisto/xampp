#!/usr/bin/env python3

import ftplib
import os

# FTP Configuration
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

def upload_env_check():
    """Upload env check script"""
    
    print("📤 Uploading environment check script...")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd('/11klassnikiru')
        
        # Upload check_env_status.php
        local_file = '/Applications/XAMPP/xamppfiles/htdocs/check_env_status.php'
        if os.path.exists(local_file):
            with open(local_file, 'rb') as f:
                ftp.storbinary('STOR check_env_status.php', f)
            print("✅ check_env_status.php uploaded!")
        else:
            print("❌ check_env_status.php not found locally")
        
        ftp.quit()
        print("\n🔍 Check environment at: https://11klassniki.ru/check_env_status.php")
        print("\nThis will show:")
        print("- Current .env file content")
        print("- Active database connection")
        print("- PHP constants")
        print("- Recommendations to fix the issue")
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")

if __name__ == "__main__":
    upload_env_check()