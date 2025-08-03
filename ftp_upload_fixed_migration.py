#!/usr/bin/env python3
"""Upload fixed migration script"""

import ftplib
from pathlib import Path

# FTP connection details
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
WEB_ROOT = "/11klassnikiru"

# File to upload
file_to_upload = "fix_missing_records_v2.php"

def main():
    print("🚀 Uploading fixed migration script...")
    
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
        print("\n📋 Visit: https://11klassniki.ru/fix_missing_records_v2.php")
        
    except Exception as e:
        print(f"❌ Error: {e}")
        return 1
    
    return 0

if __name__ == "__main__":
    exit(main())