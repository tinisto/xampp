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

# Files that need to be uploaded to fix green header issues
files_to_upload = [
    # Fix SPO page missing green header
    "pages/common/educational-institutions-all-regions/educational-institutions-all-regions.php",
    
    # Fix about page green header position (remove from content, add to template engine)
    "pages/about/about_content.php",
    "common-components/template-engine-ultimate.php"
]

def upload_file(ftp, local_path, remote_path):
    """Upload a single file to FTP server"""
    try:
        ftp.cwd(PATH)
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
        
        with open(local_path, 'rb') as f:
            ftp.storbinary(f'STOR {remote_path}', f)
        print(f"✓ Uploaded: {remote_path}")
        return True
    except Exception as e:
        print(f"✗ Failed to upload {remote_path}: {str(e)}")
        return False

def main():
    print("=" * 60)
    print("GREEN HEADER FIXES")
    print("=" * 60)
    print("Fixing two issues:")
    print("1. SPO page missing green header")
    print("2. About page green header position")
    print("=" * 60)
    
    try:
        ftp = ftplib.FTP(HOST)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✓ Connected to FTP")
        
        uploaded = 0
        failed = 0
        
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
        print("FIXES DEPLOYED")
        print("=" * 60)
        print(f"✓ Uploaded: {uploaded} files")
        print(f"✗ Failed: {failed} files")
        
        print("\n" + "=" * 60)
        print("TEST THESE PAGES:")
        print("=" * 60)
        print("1. https://11klassniki.ru/spo-all-regions")
        print("   → Should now show GREEN header")
        print("")
        print("2. https://11klassniki.ru/about")  
        print("   → Green header should look identical to news page")
        print("")
        print("3. https://11klassniki.ru/news")
        print("   → Reference for how green header should look")
        print("=" * 60)
        
    except Exception as e:
        print(f"✗ FTP Error: {str(e)}")
        return 1
    
    return 0 if failed == 0 else 1

if __name__ == "__main__":
    sys.exit(main())