#!/usr/bin/env python3

import ftplib
import os
import sys
from datetime import datetime

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

# Local base directory
LOCAL_BASE = "/Applications/XAMPP/xamppfiles/htdocs"

# Files to upload for the content wrapper test
files_to_upload = [
    "common-components/content-wrapper.php",
    "common-components/template-engine-ultimate.php",
    "pages/write/write.php",
    "pages/common/news/news.php",
    "common-components/footer.php"
]

def upload_file(ftp, local_path, remote_path):
    """Upload a single file to FTP server"""
    try:
        # Ensure we're in the base path
        ftp.cwd(PATH)
        
        # Ensure remote directory exists
        remote_dir = os.path.dirname(remote_path)
        if remote_dir:
            ensure_remote_directory(ftp, remote_dir)
            ftp.cwd(PATH)  # Return to base after creating directories
        
        # Upload the file
        with open(local_path, 'rb') as f:
            ftp.storbinary(f'STOR {remote_path}', f)
        print(f"✓ Uploaded: {remote_path}")
        return True
    except Exception as e:
        print(f"✗ Failed to upload {remote_path}: {str(e)}")
        return False

def ensure_remote_directory(ftp, directory):
    """Ensure remote directory exists, create if not"""
    # Save current directory
    try:
        current = ftp.pwd()
    except:
        current = '/'
    
    # Navigate to base path
    try:
        ftp.cwd(PATH)
    except:
        pass
    
    # Create directories recursively
    dirs = directory.split('/')
    for dir_name in dirs:
        if dir_name:
            try:
                ftp.cwd(dir_name)
            except:
                try:
                    ftp.mkd(dir_name)
                    ftp.cwd(dir_name)
                    print(f"  Created directory: {directory}")
                except:
                    pass
    
    # Return to original directory
    try:
        ftp.cwd(current)
    except:
        ftp.cwd(PATH)

def main():
    print("=" * 60)
    print("CONTENT WRAPPER TEST UPLOAD")
    print("=" * 60)
    print(f"Uploading RED background test for content wrapper")
    print(f"Target: {HOST}{PATH}")
    print("=" * 60)
    
    uploaded_files = []
    failed_files = []
    
    try:
        # Connect to FTP
        print("\nConnecting to FTP server...")
        ftp = ftplib.FTP(HOST)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✓ Connected successfully!")
        
        # Upload each file
        print("\nUploading files...")
        for file_path in files_to_upload:
            local_file = os.path.join(LOCAL_BASE, file_path)
            remote_file = file_path.replace('\\', '/')
            
            if os.path.exists(local_file):
                if upload_file(ftp, local_file, remote_file):
                    uploaded_files.append(file_path)
                else:
                    failed_files.append(file_path)
            else:
                print(f"✗ File not found: {local_file}")
                failed_files.append(file_path)
        
        # Close connection
        ftp.quit()
        
        # Summary
        print("\n" + "=" * 60)
        print("UPLOAD SUMMARY")
        print("=" * 60)
        print(f"✓ Successfully uploaded: {len(uploaded_files)} files")
        if uploaded_files:
            for f in uploaded_files:
                print(f"  - {f}")
        
        if failed_files:
            print(f"\n✗ Failed to upload: {len(failed_files)} files")
            for f in failed_files:
                print(f"  - {f}")
        
        print("\n" + "=" * 60)
        print("TEST INSTRUCTIONS:")
        print("=" * 60)
        print("1. Visit your website pages to check for RED main sections:")
        print("   - Homepage: https://11klassniki.ru/")
        print("   - Write page: https://11klassniki.ru/write")
        print("   - News page: https://11klassniki.ru/news")
        print("   - Any individual article or post")
        print("\n2. The main content area should be RED with a dark red border")
        print("\n3. Toggle between light/dark modes - it should stay red")
        print("\n4. Any page WITHOUT red means it's not using the unified wrapper")
        print("=" * 60)
        
    except Exception as e:
        print(f"\n✗ FTP Error: {str(e)}")
        return 1
    
    return 0 if not failed_files else 1

if __name__ == "__main__":
    sys.exit(main())