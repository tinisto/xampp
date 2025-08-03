#!/usr/bin/env python3

import ftplib
import os

# FTP Configuration
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

def upload_env_update():
    """Upload env update script"""
    
    print("📤 Uploading .env update script...")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd('/11klassnikiru')
        
        # Upload update_env_to_new_db.php
        local_file = '/Applications/XAMPP/xamppfiles/htdocs/update_env_to_new_db.php'
        if os.path.exists(local_file):
            with open(local_file, 'rb') as f:
                ftp.storbinary('STOR update_env_to_new_db.php', f)
            print("✅ update_env_to_new_db.php uploaded!")
        else:
            print("❌ update_env_to_new_db.php not found locally")
        
        ftp.quit()
        print("\n✅ Script uploaded!")
        print("\n🔧 Update .env at: https://11klassniki.ru/update_env_to_new_db.php")
        print("\nThis will switch your site to use the 11klassniki_new database")
        print("with the clean table structure (universities/colleges).")
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")

if __name__ == "__main__":
    upload_env_update()