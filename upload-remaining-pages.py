#!/usr/bin/env python3
"""
Upload remaining migration files
"""

import ftplib
import os
from datetime import datetime

# FTP credentials
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

# Files to upload
FILES_TO_UPLOAD = [
    "reset-password-confirm-new.php",
    "create-process.php",
    ".htaccess"
]

def upload_files():
    """Upload files to production"""
    try:
        # Connect to FTP
        print(f"Connecting to {FTP_HOST}...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        print("Connected successfully!")
        
        # Upload each file
        uploaded = 0
        for filename in FILES_TO_UPLOAD:
            local_path = f"/Applications/XAMPP/xamppfiles/htdocs/{filename}"
            
            if os.path.exists(local_path):
                with open(local_path, 'rb') as file:
                    print(f"Uploading {filename}...")
                    ftp.storbinary(f'STOR {filename}', file)
                    print(f"✅ {filename} uploaded successfully")
                    uploaded += 1
            else:
                print(f"❌ {filename} not found locally")
        
        # Close connection
        ftp.quit()
        print(f"\n✅ Uploaded {uploaded}/{len(FILES_TO_UPLOAD)} files successfully!")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    print("Remaining Pages Upload Script")
    print("=" * 50)
    print(f"Time: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    print("=" * 50)
    upload_files()