#!/usr/bin/env python3
"""Deploy database fix scripts"""

import ftplib
from pathlib import Path

# FTP connection details
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
WEB_ROOT = "/11klassnikiru"

# Files to upload
files_to_upload = [
    "fix_missing_records.php",
    "update_code_to_new_tables.php"
]

def main():
    print("🚀 Deploying database fix scripts...")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        print(f"✅ Connected to {FTP_HOST}")
        
        # Change to web root
        ftp.cwd(WEB_ROOT)
        
        # Upload files
        for file_name in files_to_upload:
            local_path = Path(file_name)
            if local_path.exists():
                with open(local_path, 'rb') as f:
                    ftp.storbinary(f'STOR {file_name}', f)
                print(f"✅ Uploaded: {file_name}")
            else:
                print(f"❌ File not found: {file_name}")
        
        ftp.quit()
        print("\n✅ FTP connection closed")
        print("\n📋 Next steps:")
        print("1. Visit: https://11klassniki.ru/fix_missing_records.php")
        print("2. Visit: https://11klassniki.ru/update_code_to_new_tables.php")
        
    except Exception as e:
        print(f"❌ Error: {e}")
        return 1
    
    return 0

if __name__ == "__main__":
    exit(main())