#!/usr/bin/env python3
"""Upload news categories check script"""

import ftplib
from pathlib import Path

# FTP connection details
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
WEB_ROOT = "/11klassnikiru"

# Files to upload
files_to_upload = ["check_news_categories.php", "spo-test-direct.php"]

def main():
    print("🚀 Uploading news check and SPO test scripts...")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        print(f"✅ Connected to {FTP_HOST}")
        
        # Upload files
        ftp.cwd(WEB_ROOT)
        for file in files_to_upload:
            local_path = Path(file)
            if local_path.exists():
                with open(local_path, 'rb') as f:
                    ftp.storbinary(f'STOR {file}', f)
                print(f"✅ Uploaded: {file}")
            else:
                print(f"❌ File not found: {file}")
        
        ftp.quit()
        print("\n✅ FTP connection closed")
        print("\n🌐 Visit:")
        print("- https://11klassniki.ru/check_news_categories.php")
        print("- https://11klassniki.ru/spo-test-direct.php")
        
    except Exception as e:
        print(f"❌ Error: {e}")
        return 1
    
    return 0

if __name__ == "__main__":
    exit(main())