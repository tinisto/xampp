#!/usr/bin/env python3
"""
Upload 404 fixes to live server
"""

import ftplib
import os
import sys

# FTP configuration
FTP_HOST = '31.31.196.154'
FTP_USER = 'u2588358'
FTP_PASS = 'i8XuW8z4'
FTP_ROOT = '/public_html'

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
        ftp.cwd(FTP_ROOT)
        print("✓ Connected to FTP server")
        
        # Files to upload
        files_to_upload = [
            {
                'local': 'pages/post/post.php',
                'remote': 'pages/post/post.php',
                'desc': 'Fixed post URL routing to handle both url_post and url_slug'
            },
            {
                'local': 'pages/category/category-data-fetch.php', 
                'remote': 'pages/category/category-data-fetch.php',
                'desc': 'Improved category data fetch with prepared statements'
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
        
        print("\nUploading 404 fixes...")
        print("=" * 50)
        
        success_count = 0
        for file_info in files_to_upload:
            print(f"\n📁 {file_info['desc']}")
            
            if upload_file(ftp, file_info['local'], file_info['remote']):
                success_count += 1
        
        print(f"\n✅ Upload completed: {success_count}/{len(files_to_upload)} files uploaded successfully")
        
        if success_count == len(files_to_upload):
            print("\n🎯 NEXT STEPS:")
            print("1. Visit /fix-404-categories.php to create missing categories")
            print("2. Visit /fix-404-manual.php for SQL commands if needed")
            print("3. Test URLs:")
            print("   - /category/ege/")
            print("   - /category/oge/")
            print("   - /category/vpr/")
            print("   - Any post URLs that were failing")
            
        ftp.quit()
        
    except Exception as e:
        print(f"❌ FTP Error: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()