#!/usr/bin/env python3

import ftplib
import os

# FTP Configuration
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

def upload_claude_guides():
    """Upload guides for using claude database"""
    
    print("📤 Uploading claude database guides...")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd('/11klassnikiru')
        
        files_to_upload = [
            'update_app_for_claude_db.php',
            'test_claude_db_connection.php'
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
        print("\n✅ Guides uploaded successfully!")
        print("\n📋 Available resources:")
        print("1. Update guide: https://11klassniki.ru/update_app_for_claude_db.php")
        print("2. Connection test: https://11klassniki.ru/test_claude_db_connection.php")
        print("3. Status check: https://11klassniki.ru/check_claude_db_status.php")
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")

if __name__ == "__main__":
    upload_claude_guides()