#!/usr/bin/env python3
"""
Final comprehensive upload of ALL necessary files
"""

import ftplib
import os

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
            print(f"✅ Uploaded: {local_path}")
            return True
            
    except Exception as e:
        print(f"❌ Failed to upload {local_path}: {str(e)}")
        return False

def main():
    """Final comprehensive upload"""
    print("🚀 FINAL COMPREHENSIVE UPLOAD - All necessary files...")
    
    # FTP Configuration
    FTP_HOST = "ftp.ipage.com"
    FTP_USER = "franko"
    FTP_PASS = "JyvR!HK2E!N55Zt"
    
    # COMPLETE list of all files needed
    critical_files = [
        # Template engine - MOST CRITICAL
        ('common-components/template-engine-ultimate.php', 'common-components/template-engine-ultimate.php'),
        
        # Header and core components
        ('common-components/header.php', 'common-components/header.php'),
        ('common-components/footer-unified.php', 'common-components/footer-unified.php'),
        
        # Layout components  
        ('common-components/content-wrapper.php', 'common-components/content-wrapper.php'),
        ('common-components/page-header.php', 'common-components/page-header.php'),
        ('common-components/typography.php', 'common-components/typography.php'),
        ('common-components/image-lazy-load.php', 'common-components/image-lazy-load.php'),
        ('common-components/card-badge.php', 'common-components/card-badge.php'),
        ('common-components/loading-spinner.php', 'common-components/loading-spinner.php'),
        
        # Category system
        ('pages/category/category.php', 'pages/category/category.php'),
        ('pages/category/category-content-unified.php', 'pages/category/category-content-unified.php'),
        ('pages/category/category-data-fetch.php', 'pages/category/category-data-fetch.php'),
        
        # Post system
        ('pages/post/post.php', 'pages/post/post.php'),
        ('pages/post/post-content.php', 'pages/post/post-content.php'),
        
        # Session and auth
        ('includes/SessionManager.php', 'includes/SessionManager.php'),
        ('includes/init.php', 'includes/init.php'),
        ('logout.php', 'logout.php'),
        
        # Functions
        ('includes/functions/pagination.php', 'includes/functions/pagination.php'),
        
        # Comments
        ('comments/comment_form.php', 'comments/comment_form.php'),
    ]
    
    try:
        print(f"📡 Connecting to {FTP_HOST}...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd('/11klassnikiru')
        print("✅ Connected to FTP server")
        
        success_count = 0
        failed_count = 0
        
        for local_path, remote_path in critical_files:
            if os.path.exists(local_path):
                if upload_file(ftp, local_path, remote_path):
                    success_count += 1
                else:
                    failed_count += 1
            else:
                print(f"⚠️  File not found: {local_path}")
                failed_count += 1
        
        ftp.quit()
        
        print(f"\n🎯 FINAL UPLOAD SUMMARY:")
        print(f"✅ Successfully uploaded: {success_count} files")
        print(f"❌ Failed uploads: {failed_count} files")
        
        if failed_count == 0:
            print("\n🎉 ALL CRITICAL FILES DEPLOYED SUCCESSFULLY!")
            print("\n🔥 The website should now work perfectly:")
            print("• Category pages should load properly")
            print("• Dark mode should work across entire site")
            print("• Mobile menu hamburger ↔ X toggle")
            print("• Purple badges for Мир увлечений")
            print("• All components should render correctly")
        else:
            print(f"\n⚠️  {failed_count} files had issues - check above for details")
            
    except Exception as e:
        print(f"❌ FTP connection failed: {str(e)}")

if __name__ == "__main__":
    main()