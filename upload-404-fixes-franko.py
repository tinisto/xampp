#!/usr/bin/env python3

import ftplib
import os
import sys

# FTP credentials for franko user
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_PATH = "/11klassnikiru/"

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
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_PATH)
        print("✓ Connected to FTP server (franko@ipage.com)")
        
        # Files to upload for 404 fixes
        files_to_upload = [
            {
                'local': 'pages/post/post.php',
                'remote': 'pages/post/post.php',
                'desc': 'Fixed post URL routing - handles both url_post and url_slug'
            },
            {
                'local': 'pages/category/category-data-fetch.php', 
                'remote': 'pages/category/category-data-fetch.php',
                'desc': 'Fixed category data fetch with prepared statements'
            },
            {
                'local': 'fix-404-categories.php',
                'remote': 'fix-404-categories.php',
                'desc': 'Script to create missing categories (ege, oge, vpr)'
            },
            {
                'local': 'fix-404-manual.php',
                'remote': 'fix-404-manual.php', 
                'desc': 'Manual SQL commands for 404 fixes'
            }
        ]
        
        print("\n🚀 Uploading 404 fixes to 11klassniki.ru...")
        print("=" * 60)
        
        success_count = 0
        for file_info in files_to_upload:
            print(f"\n📁 {file_info['desc']}")
            
            if upload_file(ftp, file_info['local'], file_info['remote']):
                success_count += 1
        
        print(f"\n✅ Upload completed: {success_count}/{len(files_to_upload)} files uploaded successfully")
        
        if success_count == len(files_to_upload):
            print("\n🎯 NEXT STEPS:")
            print("1. Visit https://11klassniki.ru/fix-404-categories.php to create missing categories")
            print("2. Test the fixed URLs:")
            print("   - https://11klassniki.ru/category/ege/")
            print("   - https://11klassniki.ru/category/oge/")
            print("   - https://11klassniki.ru/category/vpr/")
            print("   - Test any post URLs that were failing")
            print("\n3. After testing, you can remove the fix scripts from server:")
            print("   - fix-404-categories.php")
            print("   - fix-404-manual.php")
            
        ftp.quit()
        
    except Exception as e:
        print(f"❌ FTP Error: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()