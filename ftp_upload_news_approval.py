#!/usr/bin/env python3
"""Upload news approval tool"""

import ftplib
from pathlib import Path

# FTP connection details
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
WEB_ROOT = "/11klassnikiru"

# File to upload
file_to_upload = "approve_news.php"

def main():
    print("🚀 Uploading news approval tool...")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        print(f"✅ Connected to {FTP_HOST}")
        
        # Upload approve_news.php
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
        print("\n🔧 News approval tool deployed!")
        print("Visit: https://11klassniki.ru/approve_news.php")
        print("\nThis tool allows you to:")
        print("- View approved/unapproved news count")
        print("- Approve 20, 50, or all news articles")
        print("- Preview recent unapproved news")
        
    except Exception as e:
        print(f"❌ Error: {e}")
        return 1
    
    return 0

if __name__ == "__main__":
    exit(main())