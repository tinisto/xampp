#!/usr/bin/env python3
"""
Upload ALL missing files that are needed for the fixes to work
"""

import ftplib
import os
import sys

def upload_file(ftp, local_path, remote_path):
    """Upload a single file to FTP server"""
    try:
        # Navigate to remote directory
        remote_dir = os.path.dirname(remote_path)
        if remote_dir:
            # Go back to root directory first
            ftp.cwd('/11klassnikiru')
            # Navigate to target directory
            ftp.cwd(remote_dir)
        
        # Upload file
        with open(local_path, 'rb') as file:
            filename = os.path.basename(remote_path)
            ftp.storbinary(f'STOR {filename}', file)
            print(f"✅ Uploaded: {local_path} -> {remote_path}")
            return True
            
    except Exception as e:
        print(f"❌ Failed to upload {local_path}: {str(e)}")
        return False

def main():
    """Upload all missing files"""
    print("🚀 Uploading ALL missing files...")
    
    # FTP Configuration
    FTP_HOST = "ftp.ipage.com"
    FTP_USER = "franko"
    FTP_PASS = "JyvR!HK2E!N55Zt"
    
    # ALL files that need to be uploaded
    files_to_upload = [
        # Core components
        ('common-components/header.php', 'common-components/header.php'),
        ('common-components/template-engine-ultimate.php', 'common-components/template-engine-ultimate.php'),
        ('common-components/page-header.php', 'common-components/page-header.php'),
        ('common-components/content-wrapper.php', 'common-components/content-wrapper.php'),
        ('common-components/card-badge.php', 'common-components/card-badge.php'),
        ('common-components/loading-spinner.php', 'common-components/loading-spinner.php'),
        
        # Category system
        ('pages/category/category.php', 'pages/category/category.php'),
        ('pages/category/category-content-unified.php', 'pages/category/category-content-unified.php'),
        ('pages/category/category-data-fetch.php', 'pages/category/category-data-fetch.php'),
        
        # Post system
        ('pages/post/post.php', 'pages/post/post.php'),
        ('pages/post/post-content.php', 'pages/post/post-content.php'),
        
        # Comments
        ('comments/comment_form.php', 'comments/comment_form.php'),
        
        # Session management
        ('includes/SessionManager.php', 'includes/SessionManager.php'),
        ('includes/init.php', 'includes/init.php'),
        
        # Logout
        ('logout.php', 'logout.php'),
    ]
    
    try:
        print(f"📡 Connecting to {FTP_HOST}...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd('/11klassnikiru')
        print("✅ Connected to FTP server")
        
        success_count = 0
        failed_count = 0
        
        for local_path, remote_path in files_to_upload:
            if os.path.exists(local_path):
                if upload_file(ftp, local_path, remote_path):
                    success_count += 1
                else:
                    failed_count += 1
            else:
                print(f"⚠️  File not found locally: {local_path}")
                failed_count += 1
        
        ftp.quit()
        
        print(f"\n📊 Upload Summary:")
        print(f"✅ Successfully uploaded: {success_count} files")
        print(f"❌ Failed uploads: {failed_count} files")
        
        if failed_count == 0:
            print("\n🎉 All files uploaded successfully!")
        else:
            print(f"\n⚠️  {failed_count} files had issues.")
            
    except Exception as e:
        print(f"❌ FTP connection failed: {str(e)}")
        sys.exit(1)

if __name__ == "__main__":
    main()