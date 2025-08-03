#!/usr/bin/env python3

import ftplib
import os

# FTP Configuration
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

def upload_support_files():
    """Upload support guide files"""
    
    print("📤 Uploading support guide files...")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd('/11klassnikiru')
        
        files = [
            'force_new_db.php',
            'ipage_support_guide.php'
        ]
        
        for filename in files:
            local_file = f'/Applications/XAMPP/xamppfiles/htdocs/{filename}'
            if os.path.exists(local_file):
                with open(local_file, 'rb') as f:
                    ftp.storbinary(f'STOR {filename}', f)
                print(f"✅ {filename} uploaded!")
        
        ftp.quit()
        print("\n📋 Support guide: https://11klassniki.ru/ipage_support_guide.php")
        print("🔧 Force test: https://11klassniki.ru/force_new_db.php")
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")

if __name__ == "__main__":
    upload_support_files()