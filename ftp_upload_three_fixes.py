#!/usr/bin/env python3
"""
Upload files to fix:
1. Vertical alignment of category title
2. Remove dropdown icon from Categories
3. Ensure test page uses correct content file
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
            print(f"✅ Uploaded: {local_path}")
            return True
            
    except Exception as e:
        print(f"❌ Failed to upload {local_path}: {str(e)}")
        return False

def main():
    print("🚀 Uploading files with three fixes...")
    
    # FTP Configuration
    FTP_HOST = "ftp.ipage.com"
    FTP_USER = "franko"
    FTP_PASS = "JyvR!HK2E!N55Zt"
    
    # Files to upload
    files_to_upload = [
        # Fixed vertical alignment in page header
        ('common-components/page-header-compact.php', 'common-components/page-header-compact.php'),
        # Removed dropdown icon from Categories
        ('common-components/header.php', 'common-components/header.php'),
        # Updated category content file
        ('pages/category/category-content-unified.php', 'pages/category/category-content-unified.php'),
        # Ensure test page uses correct content
        ('pages/tests/tests-main.php', 'pages/tests/tests-main.php'),
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
        print("\n📝 Changes made:")
        print("✅ Fixed vertical alignment - changed from baseline to center")
        print("✅ Removed dropdown chevron icon from Categories")
        print("✅ Fixed category content to use pre-fetched data")
        print("✅ Test page using correct content file")
        print("\n🔍 Test the fixes:")
        print("https://11klassniki.ru/category/11-klassniki - Check alignment")
        print("https://11klassniki.ru - Check Categories dropdown (no chevron)")
        print("https://11klassniki.ru/tests - Check test cards display properly")
            
    except Exception as e:
        print(f"❌ FTP connection failed: {str(e)}")

if __name__ == "__main__":
    main()