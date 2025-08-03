#!/usr/bin/env python3

import ftplib
import os

# FTP Configuration
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

def upload_correct_creds():
    """Upload scripts with correct credentials"""
    
    print("📤 Uploading scripts with correct credentials...")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd('/11klassnikiru')
        
        files_to_upload = [
            'test_correct_credentials.php',
            'update_env_correct_pass.php'
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
        print("\n🔌 Test connection: https://11klassniki.ru/test_correct_credentials.php")
        print("🔧 Update .env: https://11klassniki.ru/update_env_correct_pass.php")
        print("\nCorrect credentials:")
        print("User: admin_claude")
        print("Pass: W4eZ!#9uwLmrMay")
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")

if __name__ == "__main__":
    upload_correct_creds()