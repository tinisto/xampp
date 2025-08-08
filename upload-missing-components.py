#!/usr/bin/env python3
"""Upload missing component files"""

import ftplib
import os
import sys

# FTP credentials
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_PATH = "/11klassnikiru/"

# Files to upload
files_to_upload = [
    "common-components/search-inline.php",
    "common-components/cards-grid.php",
    "common-components/filters-dropdown.php"
]

def upload_file(ftp, local_file, remote_file):
    """Upload a single file"""
    try:
        with open(local_file, 'rb') as f:
            ftp.storbinary(f'STOR {remote_file}', f)
        print(f"✓ Uploaded: {remote_file}")
        return True
    except Exception as e:
        print(f"✗ Error uploading {remote_file}: {e}")
        return False

def main():
    """Main upload function"""
    try:
        # Connect to FTP
        print("Uploading missing component files...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_PATH)
        
        # Upload files
        for file_path in files_to_upload:
            upload_file(ftp, file_path, file_path)
        
        # Close connection
        ftp.quit()
        
        print("\n✅ Component files uploaded!")
        print("\nTest the homepage now: https://11klassniki.ru")
        return 0
        
    except Exception as e:
        print(f"✗ FTP error: {e}")
        return 1

if __name__ == "__main__":
    sys.exit(main())