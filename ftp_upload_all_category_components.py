#!/usr/bin/env python3
"""
Upload all required components for category page
"""

import ftplib
import os

def upload_file(ftp, local_path, remote_path):
    """Upload a single file to FTP server"""
    try:
        # Navigate to remote directory
        remote_dir = os.path.dirname(remote_path)
        if remote_dir:
            # Reset to root
            ftp.cwd('/11klassnikiru')
            # Navigate to the directory
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
    print("🚀 Uploading all category page components...")
    
    # FTP Configuration
    FTP_HOST = "ftp.ipage.com"
    FTP_USER = "franko"
    FTP_PASS = "JyvR!HK2E!N55Zt"
    
    # All required files for category page
    files_to_upload = [
        # Category page files
        ('pages/category/category.php', 'pages/category/category.php'),
        ('pages/category/category-data-fetch.php', 'pages/category/category-data-fetch.php'),
        ('pages/category/category-content-unified.php', 'pages/category/category-content-unified.php'),
        
        # Common components
        ('common-components/content-wrapper.php', 'common-components/content-wrapper.php'),
        ('common-components/page-header.php', 'common-components/page-header.php'),
        ('common-components/card-badge.php', 'common-components/card-badge.php'),
        ('common-components/typography.php', 'common-components/typography.php'),
        ('common-components/image-lazy-load.php', 'common-components/image-lazy-load.php'),
        ('common-components/template-engine-ultimate.php', 'common-components/template-engine-ultimate.php'),
        
        # Functions
        ('includes/functions/pagination.php', 'includes/functions/pagination.php'),
    ]
    
    try:
        print(f"📡 Connecting to {FTP_HOST}...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd('/11klassnikiru')
        print("✅ Connected to FTP server")
        
        # Upload each file
        success_count = 0
        for local_path, remote_path in files_to_upload:
            if os.path.exists(local_path):
                if upload_file(ftp, local_path, remote_path):
                    success_count += 1
            else:
                print(f"⚠️  File not found locally: {local_path}")
        
        print(f"\n📊 Uploaded {success_count}/{len(files_to_upload)} files")
        
        ftp.quit()
        print("\n✅ Upload complete!")
        print("🔍 Check: https://11klassniki.ru/category/mir-uvlecheniy")
        print("🔍 Debug: https://11klassniki.ru/debug_category_error.php")
            
    except Exception as e:
        print(f"❌ FTP connection failed: {str(e)}")

if __name__ == "__main__":
    main()