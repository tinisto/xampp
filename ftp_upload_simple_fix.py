#!/usr/bin/env python3
"""Upload simple migration fix"""

import ftplib
from pathlib import Path

# FTP connection details
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
WEB_ROOT = "/11klassnikiru"

# File to upload
file_to_upload = "fix_migration_simple.php"

def main():
    print("🚀 Uploading simple migration fix...")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        print(f"✅ Connected to {FTP_HOST}")
        
        # Change to web root
        ftp.cwd(WEB_ROOT)
        
        # Upload file
        local_path = Path(file_to_upload)
        if local_path.exists():
            with open(local_path, 'rb') as f:
                ftp.storbinary(f'STOR {file_to_upload}', f)
            print(f"✅ Uploaded: {file_to_upload}")
        else:
            print(f"❌ File not found: {file_to_upload}")
        
        ftp.quit()
        print("\n✅ FTP connection closed")
        print("\n📋 Visit: https://11klassniki.ru/fix_migration_simple.php")
        print("\nThis simplified version:")
        print("- Disables foreign key checks during migration")
        print("- Uses area_id = 1 for all records (temporary)")
        print("- Processes in batches to avoid timeout")
        
    except Exception as e:
        print(f"❌ Error: {e}")
        return 1
    
    return 0

if __name__ == "__main__":
    exit(main())