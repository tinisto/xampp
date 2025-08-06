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
    "common-components/page-section-header.php",
    "common-components/content-wrapper.php",
    "index_content_modern.php",
    "pages/tests/tests-main-content.php"
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
    print("=" * 60)
    print("GREEN HEADER COMPONENT UPLOAD")
    print("=" * 60)
    print("Uploading new green page section header component")
    print("- Created reusable green header with optional search")
    print("- Removed border from red main div (testing)")
    print("- Applied to homepage and tests page")
    print("=" * 60)
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(HOST)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✓ Connected to FTP")
        
        uploaded = 0
        failed = 0
        
        # Upload each file
        for file_path in files_to_upload:
            local_file = os.path.join(LOCAL_BASE, file_path)
            if os.path.exists(local_file):
                if upload_file(ftp, local_file, file_path):
                    uploaded += 1
                else:
                    failed += 1
            else:
                print(f"✗ File not found: {local_file}")
                failed += 1
        
        ftp.quit()
        
        print("\n" + "=" * 60)
        print("UPLOAD SUMMARY")
        print("=" * 60)
        print(f"✓ Successfully uploaded: {uploaded} files")
        print(f"✗ Failed: {failed} files")
        
        print("\n" + "=" * 60)
        print("WHAT TO TEST:")
        print("=" * 60)
        print("1. Homepage: https://11klassniki.ru/")
        print("   - Should have GREEN header with search")
        print("   - Red main content (no border)")
        print("")
        print("2. Tests page: https://11klassniki.ru/tests")
        print("   - Should have GREEN header (no search)")
        print("   - Red main content (no border)")
        print("")
        print("3. Other pages should still have red content wrapper")
        print("   for consistency testing")
        print("=" * 60)
        
    except Exception as e:
        print(f"✗ FTP Error: {str(e)}")
        return 1
    
    return 0 if failed == 0 else 1

if __name__ == "__main__":
    sys.exit(main())