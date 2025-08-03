#!/usr/bin/env python3
"""Upload working pages guide and simple login"""

import ftplib
from pathlib import Path

# FTP connection details
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
WEB_ROOT = "/11klassnikiru"

# Files to upload
files_to_upload = ["working-pages-guide.php", "login-simple.php"]

def main():
    print("🚀 Uploading working pages guide...")
    
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
        print("\n🌐 Visit: https://11klassniki.ru/working-pages-guide.php")
        print("\nThis guide shows:")
        print("- Which pages are working/broken")
        print("- Alternative pages that work")
        print("- Known issues and solutions")
        
    except Exception as e:
        print(f"❌ Error: {e}")
        return 1
    
    return 0

if __name__ == "__main__":
    exit(main())