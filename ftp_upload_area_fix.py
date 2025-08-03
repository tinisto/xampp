#!/usr/bin/env python3
"""Upload area-aware migration script"""

import ftplib
from pathlib import Path

# FTP connection details
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
WEB_ROOT = "/11klassnikiru"

# File to upload
file_to_upload = "fix_missing_records_with_areas.php"

def main():
    print("🚀 Uploading area-aware migration script...")
    
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
        print("\n📋 Visit: https://11klassniki.ru/fix_missing_records_with_areas.php")
        print("\nThis script will:")
        print("- Properly handle area_id foreign key constraints")
        print("- Create areas if needed for regions")
        print("- Process records in smaller batches (20 at a time)")
        
    except Exception as e:
        print(f"❌ Error: {e}")
        return 1
    
    return 0

if __name__ == "__main__":
    exit(main())