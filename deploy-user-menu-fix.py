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
    'dashboard-professional.php': 'dashboard-professional.php',
    'dashboard-users-professional.php': 'dashboard-users-professional.php'
}

try:
    print("🔧 Deploying User Menu Dropdown Fix")
    print("===================================")
    
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
        print("\n✅ User menu dropdown fix deployed successfully!")
        print("\n🔧 New Features:")
        print("   - 👤 User menu is now clickable with dropdown")
        print("   - 📱 Beautiful dropdown menu with user info")
        print("   - 🎯 Quick access to profile, dashboard, backup, logout")
        print("   - ⚡ Smooth animations and hover effects")
        print("   - 🎨 Consistent design across all dashboards")
        
        print("\n🔗 Test the user menu:")
        print("Main Dashboard: https://11klassniki.ru/dashboard")
        print("Users Dashboard: https://11klassniki.ru/dashboard/users")
        
        print("\n📋 User menu features:")
        print("   - Click username on right side of header")
        print("   - Dropdown shows with user avatar and info")
        print("   - Links to: Profile, Dashboard, Users, Backup, Home, Logout")
        print("   - Click outside or press Escape to close")
        print("   - Hover effects and professional styling")
        
        print("\n💡 User menu now works properly on both dashboards!")
        
    else:
        print("\n⚠️  Some files failed to upload. Please check the errors above.")
    
except Exception as e:
    print(f"\n❌ Error: {str(e)}")