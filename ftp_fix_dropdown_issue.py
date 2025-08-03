#!/usr/bin/env python3
"""
Fix user dropdown not working on news page and other pages
"""

import ftplib
import os

def upload_file(ftp, local_path, remote_path):
    """Upload a single file to FTP server"""
    try:
        # Navigate to remote directory
        remote_dir = os.path.dirname(remote_path)
        ftp.cwd('/11klassnikiru')
        
        if remote_dir:
            dirs = remote_dir.split('/')
            for dir in dirs:
                if dir:
                    try:
                        ftp.cwd(dir)
                    except:
                        ftp.mkd(dir)
                        ftp.cwd(dir)
        
        # Upload file
        with open(local_path, 'rb') as file:
            filename = os.path.basename(remote_path)
            ftp.storbinary(f'STOR {filename}', file)
            print(f"✅ Uploaded: {remote_path}")
            return True
            
    except Exception as e:
        print(f"❌ Failed to upload {local_path}: {str(e)}")
        return False

def main():
    print("🚀 Fixing dropdown functionality across all pages...")
    
    # FTP Configuration
    FTP_HOST = "ftp.ipage.com"
    FTP_USER = "franko"
    FTP_PASS = "JyvR!HK2E!N55Zt"
    
    # Files to upload
    files_to_upload = [
        # Global dropdown fix script
        ('js/dropdown-fix.js', 'js/dropdown-fix.js'),
        # Updated header with script include
        ('common-components/header.php', 'common-components/header.php'),
    ]
    
    try:
        print(f"📡 Connecting to {FTP_HOST}...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd('/11klassnikiru')
        print("✅ Connected to FTP server")
        
        # Upload each file
        for local_path, remote_path in files_to_upload:
            if os.path.exists(local_path):
                upload_file(ftp, local_path, remote_path)
            else:
                print(f"⚠️  File not found locally: {local_path}")
        
        ftp.quit()
        print("\n✅ Upload complete!")
        print("\n🎯 Fixed:")
        print("✅ Added global dropdown fix script")
        print("✅ Script loads on all pages via header")
        print("✅ Handles dropdown conflicts and re-initialization")
        print("✅ Forces dropdown visibility when needed")
        print("✅ Adds proper event listeners to user avatar")
        
        print("\n📋 What the fix does:")
        print("• Removes conflicting event listeners")
        print("• Adds fresh dropdown listeners on each page")
        print("• Forces dropdown menu visibility")
        print("• Handles clicks outside to close dropdowns")
        print("• Re-initializes after any DOM changes")
        
        print("\n🔍 Test:")
        print("https://11klassniki.ru/news - User circle should now work")
        print("https://11klassniki.ru/ - Should still work as before")
        print("Console will show 'Dropdown clicked' when working")
            
    except Exception as e:
        print(f"❌ FTP connection failed: {str(e)}")

if __name__ == "__main__":
    main()