#!/usr/bin/env python3

import ftplib
import os

# FTP Configuration
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

def upload_db_access_check():
    """Upload database access check script"""
    
    print("📤 Uploading database access check script...")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd('/11klassnikiru')
        
        files_to_upload = [
            'check_db_access.php',
            'update_env_for_claude.php'
        ]
        
        for filename in files_to_upload:
            local_file = f'/Applications/XAMPP/xamppfiles/htdocs/{filename}'
            if os.path.exists(local_file):
                with open(local_file, 'rb') as f:
                    ftp.storbinary(f'STOR {filename}', f)
                print(f"✅ {filename} uploaded!")
            else:
                print(f"❌ {filename} not found locally")
        
        ftp.quit()
        print("\n✅ Scripts uploaded!")
        print("\n🔍 Check database access: https://11klassniki.ru/check_db_access.php")
        print("📝 Update .env file: https://11klassniki.ru/update_env_for_claude.php")
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")

if __name__ == "__main__":
    upload_db_access_check()