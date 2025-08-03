#!/usr/bin/env python3
"""Upload debug login page"""

import ftplib
from pathlib import Path

# FTP connection details
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
WEB_ROOT = "/11klassnikiru"

# File to upload
file_to_upload = "login-debug.php"

def main():
    print("🚀 Uploading debug login page...")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        print(f"✅ Connected to {FTP_HOST}")
        
        # Upload file
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
        print(f"\n🌐 Visit: https://11klassniki.ru/{file_to_upload}")
        print("\nThis debug page will show:")
        print("- If the test user exists")
        print("- If the password verification works")
        print("- What's causing the login failure")
        
    except Exception as e:
        print(f"❌ Error: {e}")
        return 1
    
    return 0

if __name__ == "__main__":
    exit(main())