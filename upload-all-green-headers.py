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

# All files with green headers applied
files_to_upload = [
    # Write pages
    "pages/write/write-simple.php",
    "pages/write/write-form-modern.php",
    
    # News pages
    "pages/common/news/news.php",
    
    # About page
    "pages/about/about_content.php",
    
    # Category pages (from agent task)
    "pages/category/category-content-unified.php",
    
    # Search pages (from agent task)
    "pages/search/search-content-secure.php",
    
    # SPO/VPO pages (from agent task)
    "pages/common/vpo-spo/spo-vpo-content.php",
    "vpo-all-regions-standalone.php",
    
    # Green header component (make sure it's uploaded)
    "common-components/page-section-header.php"
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
    print("=" * 70)
    print("GREEN HEADERS DEPLOYMENT - ALL MAIN PAGES")
    print("=" * 70)
    print("Deploying green headers to all main site pages:")
    print("- Write pages (contact forms)")
    print("- News pages (listing and articles)")
    print("- About page")
    print("- Category pages")
    print("- Search pages")
    print("- School/VPO/SPO listing pages")
    print("- Green header component")
    print("=" * 70)
    
    uploaded = 0
    failed = 0
    failed_files = []
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(HOST)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✓ Connected to FTP server")
        print()
        
        # Upload each file
        for file_path in files_to_upload:
            local_file = os.path.join(LOCAL_BASE, file_path)
            if os.path.exists(local_file):
                if upload_file(ftp, local_file, file_path):
                    uploaded += 1
                else:
                    failed += 1
                    failed_files.append(file_path)
            else:
                print(f"✗ File not found: {local_file}")
                failed += 1
                failed_files.append(file_path)
        
        ftp.quit()
        
        print("\n" + "=" * 70)
        print("DEPLOYMENT SUMMARY")
        print("=" * 70)
        print(f"✓ Successfully uploaded: {uploaded} files")
        if failed > 0:
            print(f"✗ Failed uploads: {failed} files")
            for f in failed_files:
                print(f"  - {f}")
        
        print("\n" + "=" * 70)
        print("PAGES TO TEST WITH GREEN HEADERS:")
        print("=" * 70)
        print("✅ Homepage: https://11klassniki.ru/")
        print("✅ Write page: https://11klassniki.ru/write") 
        print("✅ Tests page: https://11klassniki.ru/tests")
        print("✅ News page: https://11klassniki.ru/news")
        print("✅ About page: https://11klassniki.ru/about")
        print("✅ Category pages: https://11klassniki.ru/category/[category-name]")
        print("✅ Search page: https://11klassniki.ru/search")
        print("✅ VPO listings: https://11klassniki.ru/vpo-all-regions")
        print("✅ SPO/VPO individual pages")
        print("\n🎯 All pages should now display consistent GREEN headers!")
        print("🔴 Red main content areas remain for testing the unified wrapper")
        print("=" * 70)
        
    except Exception as e:
        print(f"✗ FTP Connection Error: {str(e)}")
        return 1
    
    return 0 if failed == 0 else 1

if __name__ == "__main__":
    sys.exit(main())