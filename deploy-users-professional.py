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
    '.htaccess': '.htaccess',
    'dashboard-users-professional.php': 'dashboard-users-professional.php',
    'pages/dashboard/users-dashboard/users-view/admin-users-content.php': 'pages/dashboard/users-dashboard/users-view/admin-users-content.php'
}

try:
    print("🔧 Deploying Professional Users Dashboard")
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
        print("\n✅ Professional Users Dashboard deployed successfully!")
        print("\n🔧 Changes Applied:")
        print("   - Updated .htaccess to use dashboard-users-professional.php")
        print("   - Created beautiful professional users dashboard")
        print("   - Fixed avatar field errors with proper null handling")
        
        print("\n🎨 Professional Users Dashboard Features:")
        print("   - ✨ Same beautiful design as main dashboard")
        print("   - 🎯 Clean users table with proper styling")
        print("   - 👤 User avatars and role badges")
        print("   - 📱 Responsive design")
        print("   - 🔍 Pagination for large user lists")
        print("   - ⚡ Quick actions (delete, suspend/unsuspend)")
        print("   - 🎨 Matching sidebar navigation")
        
        print("\n🔗 Test the new users dashboard:")
        print("Via navigation: https://11klassniki.ru/dashboard (click Пользователи)")
        print("Direct: https://11klassniki.ru/dashboard/users")  
        
        print("\n📋 What should work now:")
        print("   - Beautiful matching design with main dashboard")
        print("   - No more 'Undefined index: avatar' errors")
        print("   - Proper Russian interface")
        print("   - Smooth navigation between dashboard pages")
        
    else:
        print("\n⚠️  Some files failed to upload. Please check the errors above.")
    
except Exception as e:
    print(f"\n❌ Error: {str(e)}")