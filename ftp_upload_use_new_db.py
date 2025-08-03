#!/usr/bin/env python3

import ftplib
import os

# FTP Configuration
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

def upload_use_new_db():
    """Upload script to use new database"""
    
    print("📤 Uploading use new database script...")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd('/11klassnikiru')
        
        # Upload use_new_database.php
        local_file = '/Applications/XAMPP/xamppfiles/htdocs/use_new_database.php'
        if os.path.exists(local_file):
            with open(local_file, 'rb') as f:
                ftp.storbinary('STOR use_new_database.php', f)
            print("✅ use_new_database.php uploaded!")
        else:
            print("❌ use_new_database.php not found locally")
        
        ftp.quit()
        print("\n✅ Script uploaded!")
        print("\n🔍 Check options at: https://11klassniki.ru/use_new_database.php")
        print("\nIMPORTANT: admin_claude has access to 11klassniki_new (not 11klassniki_claude)")
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")

if __name__ == "__main__":
    upload_use_new_db()