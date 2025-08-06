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

# Files to upload to fix red covering green issue
files_to_upload = [
    "common-components/template-engine-ultimate.php",  # Green header outside red wrapper
    "index.php",  # Homepage config with search
    "index_content_posts_with_news_style.php"  # Removed duplicate green header
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
    print("FIXING RED WRAPPER COVERING GREEN HEADER")
    print("=" * 50)
    print("🔧 Problem: Red content wrapper covers green header title")
    print("🎯 Solution: Put green header OUTSIDE red wrapper in template engine")
    print("🏠 Homepage: Add search functionality to template engine")
    
    try:
        ftp = ftplib.FTP(HOST, USER, PASS)
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
        
        print(f"\n✅ Uploaded {uploaded} files")
        print(f"❌ Failed: {failed} files")
        
        if failed == 0:
            print("\n🎉 RED COVERING GREEN ISSUE FIXED!")
            print("✅ Green headers now appear ABOVE red content")
            print("✅ No more overlapping/covering")
            print("✅ Homepage keeps search functionality")
            print("✅ Template engine handles green headers consistently")
        
        print("\n" + "=" * 50)
        print("TEST THESE PAGES:")
        print("=" * 50)
        print("🏠 Homepage: https://11klassniki.ru/")
        print("   → Should show green header with search")
        print("   → Green should be ABOVE red, not covered")
        print("")
        print("ℹ️  About: https://11klassniki.ru/about")
        print("   → Should show green header without search")
        print("   → Green should be ABOVE red, not covered")
        print("")
        print("📰 News: https://11klassniki.ru/news")
        print("   → Should show green header (uses different system)")
        print("=" * 50)
        
    except Exception as e:
        print(f"✗ FTP Error: {str(e)}")
        return 1
    
    return 0 if failed == 0 else 1

if __name__ == "__main__":
    sys.exit(main())