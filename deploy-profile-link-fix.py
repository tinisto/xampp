#!/usr/bin/env python3
import ftplib
from datetime import datetime

FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
REMOTE_DIR = '/11klassnikiru'

def upload_file(ftp, local_file, remote_file):
    """Upload a single file to FTP server"""
    try:
        with open(local_file, 'rb') as f:
            ftp.storbinary(f'STOR {remote_file}', f)
        print(f"✅ Uploaded: {remote_file}")
        return True
    except Exception as e:
        print(f"❌ Failed to upload {remote_file}: {str(e)}")
        return False

try:
    print("🔧 Deploying Profile Link Fix for Account Page")
    print("=" * 60)
    print(f"⏰ Time: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    
    # Connect to FTP
    print(f"\n📡 Connecting to {FTP_HOST}...")
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(REMOTE_DIR)
    print("✅ Connected successfully!")
    
    # Files to upload
    files_to_upload = [
        {
            'local': 'common-components/header.php',
            'remote': 'common-components/header.php',
            'description': 'Main header component'
        },
        {
            'local': 'dashboard-professional.php',
            'remote': 'dashboard-professional.php',
            'description': 'Professional dashboard'
        },
        {
            'local': 'dashboard-create-content.php',
            'remote': 'dashboard-create-content.php',
            'description': 'Create content dashboard'
        },
        {
            'local': 'dashboard-create-content-unified.php',
            'remote': 'dashboard-create-content-unified.php',
            'description': 'Unified create content dashboard'
        },
        {
            'local': 'dashboard-create-content-unified-backup.php',
            'remote': 'dashboard-create-content-unified-backup.php',
            'description': 'Unified create content backup'
        },
        {
            'local': 'dashboard-edit-content.php',
            'remote': 'dashboard-edit-content.php',
            'description': 'Edit content dashboard'
        },
        {
            'local': 'dashboard-news-management.php',
            'remote': 'dashboard-news-management.php',
            'description': 'News management dashboard'
        },
        {
            'local': 'dashboard-posts-management.php',
            'remote': 'dashboard-posts-management.php',
            'description': 'Posts management dashboard'
        },
        {
            'local': 'dashboard-users-professional.php',
            'remote': 'dashboard-users-professional.php',
            'description': 'Users dashboard'
        },
        {
            'local': 'dashboard-with-user-menu.php',
            'remote': 'dashboard-with-user-menu.php',
            'description': 'Dashboard with user menu'
        }
    ]
    
    print("\n📤 Uploading profile link fixes...")
    success_count = 0
    
    for file_info in files_to_upload:
        print(f"\n📁 {file_info['description']}")
        if upload_file(ftp, file_info['local'], file_info['remote']):
            success_count += 1
    
    ftp.quit()
    
    if success_count == len(files_to_upload):
        print(f"\n✅ All {success_count} files deployed successfully!")
        
        print("\n🎯 What's Fixed:")
        print("   - Profile link is now hidden when on /account page")
        print("   - No redundant 'Profile' link that leads to same page")
        print("   - Applied to all user dropdown menus site-wide")
        print("   - Consistent behavior across all pages")
        
        print("\n🌐 Test the Fix:")
        print("   1. Go to https://11klassniki.ru/account/")
        print("   2. Click on the user menu dropdown")
        print("   3. You should NOT see the 'Profile' link")
        print("   4. Go to any other page (like home)")
        print("   5. The 'Profile' link should be visible there")
        
    else:
        print(f"\n⚠️  Some files failed ({success_count}/{len(files_to_upload)})")
    
except Exception as e:
    print(f"❌ Error: {str(e)}")