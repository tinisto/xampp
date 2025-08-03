#!/usr/bin/env python3
"""Upload login fixes and test user creator"""

import ftplib
from pathlib import Path

# FTP connection details
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
WEB_ROOT = "/11klassnikiru"

# Files to upload
files_to_upload = [
    "login-simple.php",
    "create_test_user.php",
    "pages/login/login_process_simple.php"
]

def main():
    print("🚀 Uploading login fixes...")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        print(f"✅ Connected to {FTP_HOST}")
        
        for file in files_to_upload:
            local_path = Path(file)
            if local_path.exists():
                # Navigate to correct directory
                if '/' in file:
                    parts = file.split('/')
                    remote_dir = WEB_ROOT + '/' + '/'.join(parts[:-1])
                    ftp.cwd(remote_dir)
                    filename = parts[-1]
                else:
                    ftp.cwd(WEB_ROOT)
                    filename = file
                
                with open(local_path, 'rb') as f:
                    ftp.storbinary(f'STOR {filename}', f)
                print(f"✅ Uploaded: {file}")
            else:
                print(f"❌ File not found: {file}")
        
        ftp.quit()
        print("\n✅ FTP connection closed")
        print("\n🌐 Visit:")
        print("- https://11klassniki.ru/create_test_user.php - Create test account")
        print("- https://11klassniki.ru/login-simple.php - Simple login page")
        
    except Exception as e:
        print(f"❌ Error: {e}")
        return 1
    
    return 0

if __name__ == "__main__":
    exit(main())