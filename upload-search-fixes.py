#!/usr/bin/env python3

import ftplib
import os
import sys

# FTP credentials
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
        print("✓ Connected to FTP server")
        
        # Files to upload for search functionality fixes
        files_to_upload = [
            {
                'local': 'pages/search/search.php',
                'remote': 'pages/search/search.php',
                'desc': 'Fixed search page - removed missing construction check'
            },
            {
                'local': 'pages/search/search-process.php', 
                'remote': 'pages/search/search-process.php',
                'desc': 'Fixed search process - removed missing construction check'
            },
            {
                'local': 'pages/search/search-process-content.php',
                'remote': 'pages/search/search-process-content.php',
                'desc': 'Fixed search results - proper URL field handling for posts'
            }
        ]
        
        print("\n🔍 Uploading search functionality fixes...")
        print("=" * 55)
        
        success_count = 0
        for file_info in files_to_upload:
            print(f"\n📁 {file_info['desc']}")
            
            if upload_file(ftp, file_info['local'], file_info['remote']):
                success_count += 1
        
        print(f"\n✅ Upload completed: {success_count}/{len(files_to_upload)} files uploaded successfully")
        
        if success_count == len(files_to_upload):
            print("\n🎯 SEARCH FIXES APPLIED:")
            print("• Removed missing construction check references")
            print("• Fixed post URL handling to use url_slug (prevents 404s)")
            print("• Search now uses COALESCE(url_slug, url_post) for compatibility")
            print("• Updated template engine paths for proper includes")
            print("\n🔍 SEARCH FUNCTIONALITY:")
            print("• /search - Main search page with form")  
            print("• /search-process - Search results page")
            print("• Searches: Schools, Posts/Articles, News")
            print("• Uses prepared statements for security")
            print("\n🚀 Search should now be fully functional!")
            
        ftp.quit()
        
    except Exception as e:
        print(f"❌ FTP Error: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()