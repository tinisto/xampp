#!/usr/bin/env python3

import ftplib
import os

# FTP Configuration
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

def upload_refresh_test():
    """Upload refresh test"""
    
    print("📤 Uploading refresh test...")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd('/11klassnikiru')
        
        # Upload refresh_test.php
        local_file = '/Applications/XAMPP/xamppfiles/htdocs/refresh_test.php'
        if os.path.exists(local_file):
            with open(local_file, 'rb') as f:
                ftp.storbinary('STOR refresh_test.php', f)
            print("✅ refresh_test.php uploaded!")
        
        ftp.quit()
        print("\n🔄 Test with caching disabled: https://11klassniki.ru/refresh_test.php")
        print("\nNow that iPage disabled caching:")
        print("1. Visit the test URL above")
        print("2. Clear your browser cache (Ctrl+F5)")
        print("3. Wait 10-15 minutes for changes to take effect")
        print("4. Test again with: https://11klassniki.ru/test_new_structure.php")
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")

if __name__ == "__main__":
    upload_refresh_test()