#!/usr/bin/env python3
"""
Deploy mobile menu and theme fixes to live server
Files modified:
- header.php (mobile menu hamburger to X)
- template-engine-ultimate.php (dark mode fixes)
- page-header.php (compact margins)
- category-content-unified.php (category colors)
- post-content.php (dark mode support)
- comment_form.php (theme variables)
"""

import ftplib
import os
import sys
from pathlib import Path

def get_ftp_credentials():
    """Get FTP credentials"""
    # Use the correct credentials from recent deployment scripts
    return "ftp.ipage.com", "franko", "JyvR!HK2E!N55Zt"

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
    """Main deployment function"""
    print("🚀 Deploying mobile menu and theme fixes...")
    
    # Get FTP credentials
    ftp_host, ftp_user, ftp_pass = get_ftp_credentials()
    if not all([ftp_host, ftp_user, ftp_pass]):
        sys.exit(1)
    
    # Files to upload (local_path, remote_path)
    files_to_upload = [
        ('common-components/header.php', 'common-components/header.php'),
        ('common-components/template-engine-ultimate.php', 'common-components/template-engine-ultimate.php'),
        ('common-components/page-header.php', 'common-components/page-header.php'),
        ('pages/category/category-content-unified.php', 'pages/category/category-content-unified.php'),
        ('pages/post/post-content.php', 'pages/post/post-content.php'),
        ('comments/comment_form.php', 'comments/comment_form.php'),
    ]
    
    try:
        # Connect to FTP server
        print(f"📡 Connecting to {ftp_host}...")
        ftp = ftplib.FTP(ftp_host)
        ftp.login(ftp_user, ftp_pass)
        print("✅ Connected to FTP server")
        
        # Change to web root directory
        ftp.cwd('/11klassnikiru')
        
        success_count = 0
        failed_count = 0
        
        # Upload each file
        for local_path, remote_path in files_to_upload:
            if os.path.exists(local_path):
                if upload_file(ftp, local_path, remote_path):
                    success_count += 1
                else:
                    failed_count += 1
            else:
                print(f"⚠️  File not found: {local_path}")
                failed_count += 1
        
        ftp.quit()
        
        # Summary
        print(f"\n📊 Deployment Summary:")
        print(f"✅ Successfully uploaded: {success_count} files")
        print(f"❌ Failed uploads: {failed_count} files")
        
        if failed_count == 0:
            print("\n🎉 All files deployed successfully!")
            print("\nChanges deployed:")
            print("• Mobile menu hamburger ↔ X toggle")
            print("• Dark mode theme fixes")
            print("• Compact header margins")
            print("• Dynamic category badge colors")
            print("• Post page dark mode support")
            print("• Comment form theme support")
        else:
            print(f"\n⚠️  {failed_count} files failed to upload. Please check the errors above.")
            
    except Exception as e:
        print(f"❌ FTP connection failed: {str(e)}")
        sys.exit(1)

if __name__ == "__main__":
    main()