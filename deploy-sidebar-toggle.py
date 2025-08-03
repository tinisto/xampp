#!/usr/bin/env python3
import ftplib
import os

FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
REMOTE_DIR = '/11klassnikiru'

def upload_file(ftp, local_file, remote_file):
    """Upload a single file to FTP server"""
    try:
        with open(local_file, 'rb') as f:
            ftp.storbinary(f'STOR {remote_file}', f)
        print(f"✅ Updated: {remote_file}")
        return True
    except Exception as e:
        print(f"❌ Failed to upload {remote_file}: {str(e)}")
        return False

# Files to deploy
FILES_TO_DEPLOY = {
    'dashboard-users-professional.php': 'dashboard-users-professional.php'
}

try:
    print("🔧 Deploying Sidebar Toggle Functionality")
    print("=========================================")
    
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(REMOTE_DIR)
    
    success_count = 0
    total_files = len(FILES_TO_DEPLOY)
    
    for local_file, remote_file in FILES_TO_DEPLOY.items():
        if os.path.exists(local_file):
            if upload_file(ftp, local_file, remote_file):
                success_count += 1
        else:
            print(f"⚠️  Local file not found: {local_file}")
    
    ftp.quit()
    
    print(f"\n📊 Deployment Summary:")
    print(f"   - Total files: {total_files}")
    print(f"   - Successfully uploaded: {success_count}")
    print(f"   - Failed: {total_files - success_count}")
    
    if success_count == total_files:
        print("\n✅ Sidebar toggle functionality deployed successfully!")
        print("\n🔧 New Features Added:")
        print("   - ☰ Toggle button now visible on desktop (top-left of header)")
        print("   - 🎛️ Click to collapse/expand sidebar")
        print("   - 🎨 Smooth animations when toggling")
        print("   - 📱 Works on both desktop and mobile")
        
        print("\n🔗 Test the sidebar toggle:")
        print("https://11klassniki.ru/dashboard/users")
        
        print("\n📋 How to use:")
        print("   1. Look for the ☰ (hamburger) button next to 'Пользователи' title")
        print("   2. Click it to hide the left sidebar")
        print("   3. Click again to show the sidebar")
        print("   4. Content area expands when sidebar is hidden")
        print("   5. Smooth animation transitions")
        
        print("\n💡 Perfect for:")
        print("   - Getting more screen space for the users table")
        print("   - Better mobile experience")
        print("   - Focus mode when working with data")
        
    else:
        print("\n⚠️  Some files failed to upload. Please check the errors above.")
    
except Exception as e:
    print(f"\n❌ Error: {str(e)}")