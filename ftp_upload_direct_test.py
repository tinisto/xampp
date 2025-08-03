#!/usr/bin/env python3

import ftplib
import os

# FTP Configuration
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

def upload_direct_test():
    """Upload direct database test script"""
    
    print("📤 Uploading direct database test script...")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd('/11klassnikiru')
        
        files = [
            'direct_db_test.php',
            'force_reload_env.php'
        ]
        
        for filename in files:
            local_file = f'/Applications/XAMPP/xamppfiles/htdocs/{filename}'
            if os.path.exists(local_file):
                with open(local_file, 'rb') as f:
                    ftp.storbinary(f'STOR {filename}', f)
                print(f"✅ {filename} uploaded!")
            else:
                print(f"❌ {filename} not found")
        
        ftp.quit()
        print("\n🔌 Direct test: https://11klassniki.ru/direct_db_test.php")
        print("🔄 Force reload: https://11klassniki.ru/force_reload_env.php")
        print("\nThe direct test will show if the new database is accessible")
        print("without using any cached configuration.")
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")

if __name__ == "__main__":
    upload_direct_test()