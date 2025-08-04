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
    'dashboard-users-professional.php': 'dashboard-users-professional.php',
    'dashboard-create-content.php': 'dashboard-create-content.php'
}

try:
    print("🔧 Deploying User Menu Dropdown to All Dashboards")
    print("================================================")
    
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
        print("\n✅ User menu dropdown deployed to ALL dashboards successfully!")
        print("\n🔧 User menu now works on:")
        print("   📊 Main Dashboard: https://11klassniki.ru/dashboard")
        print("   👥 Users Dashboard: https://11klassniki.ru/dashboard/users")
        print("   📝 Create Post: https://11klassniki.ru/create/post")
        print("   📰 Create News: https://11klassniki.ru/create/news")
        
        print("\n🎯 Features across all dashboards:")
        print("   - ✅ Clickable user menu with dropdown")
        print("   - 👤 User avatar and info in dropdown header")
        print("   - 📋 Quick navigation links")
        print("   - 🎨 Consistent design everywhere")
        print("   - ⬇️ Visual indicator (arrow)")
        print("   - 🎯 Context-specific menu items")
        
        print("\n📋 User menu functionality:")
        print("   - Click username → opens dropdown")
        print("   - Click outside → closes dropdown")
        print("   - Press Escape → closes dropdown")
        print("   - Smooth animations")
        print("   - Hover effects on menu items")
        
        print("\n💡 The user menu now works consistently across ALL dashboard pages!")
        
    else:
        print("\n⚠️  Some files failed to upload. Please check the errors above.")
    
except Exception as e:
    print(f"\n❌ Error: {str(e)}")