#!/usr/bin/env python3

import ftplib
import os

# FTP Configuration
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

def upload_college_migration():
    """Upload college migration scripts"""
    
    print("📤 Uploading college migration scripts...")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd('/11klassnikiru')
        
        files_to_upload = [
            'check_missing_colleges_detailed.php',
            'migrate_remaining_colleges.php'
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
        print("\n✅ All files uploaded successfully!")
        print("\nNext steps:")
        print("1. Go back to https://11klassniki.ru/fix_missing_data.php")
        print("2. Click 'Full Copy All Missing Data' to migrate the remaining colleges")
        print("3. Or visit https://11klassniki.ru/check_missing_colleges_detailed.php for detailed analysis")
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")

if __name__ == "__main__":
    upload_college_migration()