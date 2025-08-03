#!/usr/bin/env python3
"""Upload fixed news approval tool"""

import ftplib
from pathlib import Path

# FTP connection details
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
WEB_ROOT = "/11klassnikiru"

# File to upload
file_to_upload = "approve_news_fixed.php"

def main():
    print("🚀 Uploading fixed news approval tool...")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        print(f"✅ Connected to {FTP_HOST}")
        
        # Upload approve_news_fixed.php
        ftp.cwd(WEB_ROOT)
        local_path = Path(file_to_upload)
        if local_path.exists():
            with open(local_path, 'rb') as f:
                ftp.storbinary(f'STOR {file_to_upload}', f)
            print(f"✅ Uploaded: {file_to_upload}")
        else:
            print(f"❌ File not found: {file_to_upload}")
            return 1
        
        ftp.quit()
        print("\n✅ FTP connection closed")
        print("\n🔧 Fixed news approval tool deployed!")
        print("Visit: https://11klassniki.ru/approve_news_fixed.php")
        print("\nThis fixed version:")
        print("- Checks if is_approved column exists")
        print("- Can add the column if missing")
        print("- Handles NULL values properly")
        print("- Shows table structure for debugging")
        
    except Exception as e:
        print(f"❌ Error: {e}")
        return 1
    
    return 0

if __name__ == "__main__":
    exit(main())