#!/usr/bin/env python3
"""Upload debug login processor"""

import ftplib
from pathlib import Path

# FTP connection details
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
WEB_ROOT = "/11klassnikiru"

# File to upload
file_to_upload = "login-process-debug.php"

def main():
    print("🚀 Uploading debug login processor...")
    
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
        print("\n🌐 The login-simple.php page is already configured to use this processor")
        print("Test it at: https://11klassniki.ru/login-simple.php")
        print("\nThis processor:")
        print("- Uses the same database connection as the debug page")
        print("- Sets all necessary session variables")
        print("- Should fix the 'Invalid email or password' error")
        
    except Exception as e:
        print(f"❌ Error: {e}")
        return 1
    
    return 0

if __name__ == "__main__":
    exit(main())