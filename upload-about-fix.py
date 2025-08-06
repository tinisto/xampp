#!/usr/bin/env python3

import ftplib
import os
import sys

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

# Local base directory
LOCAL_BASE = "/Applications/XAMPP/xamppfiles/htdocs"

# Files to upload
files_to_upload = [
    "pages/about/about.php",
    "pages/about/about_content.php",
    "test-about-direct.php"
]

def upload_file(ftp, local_path, remote_path):
    """Upload a single file to FTP server"""
    try:
        # Ensure we're in the base path
        ftp.cwd(PATH)
        
        # Ensure remote directory exists
        remote_dir = os.path.dirname(remote_path)
        if remote_dir:
            dirs = remote_dir.split('/')
            for dir_name in dirs:
                if dir_name:
                    try:
                        ftp.cwd(dir_name)
                    except:
                        try:
                            ftp.mkd(dir_name)
                            ftp.cwd(dir_name)
                        except:
                            pass
            ftp.cwd(PATH)
        
        # Upload the file
        with open(local_path, 'rb') as f:
            ftp.storbinary(f'STOR {remote_path}', f)
        print(f"✓ Uploaded: {remote_path}")
        return True
    except Exception as e:
        print(f"✗ Failed to upload {remote_path}: {str(e)}")
        return False

def main():
    print("Uploading about page files...")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(HOST)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✓ Connected to FTP")
        
        # Upload each file
        for file_path in files_to_upload:
            local_file = os.path.join(LOCAL_BASE, file_path)
            if os.path.exists(local_file):
                upload_file(ftp, local_file, file_path)
            else:
                print(f"✗ File not found: {local_file}")
        
        ftp.quit()
        print("\nDone! Test at:")
        print("- https://11klassniki.ru/test-about-direct.php")
        print("- https://11klassniki.ru/about")
        
    except Exception as e:
        print(f"✗ FTP Error: {str(e)}")
        return 1
    
    return 0

if __name__ == "__main__":
    sys.exit(main())