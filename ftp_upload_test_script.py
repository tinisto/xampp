#!/usr/bin/env python3

import ftplib
import os

# FTP Configuration
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

def upload_test_script():
    """Upload test script for new structure"""
    
    print("📤 Uploading test script...")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd('/11klassnikiru')
        
        # Upload test_new_structure.php
        local_file = '/Applications/XAMPP/xamppfiles/htdocs/test_new_structure.php'
        if os.path.exists(local_file):
            with open(local_file, 'rb') as f:
                ftp.storbinary('STOR test_new_structure.php', f)
            print("✅ test_new_structure.php uploaded!")
        else:
            print("❌ test_new_structure.php not found locally")
        
        ftp.quit()
        print("\n🧪 Test the new structure at: https://11klassniki.ru/test_new_structure.php")
        print("\nThis will show:")
        print("- Database connection status")
        print("- Table structure verification")
        print("- Sample data with test links")
        print("- Links to test various pages")
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")

if __name__ == "__main__":
    upload_test_script()