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
    '.htaccess': '.htaccess'
}

try:
    print("🔧 Deploying Fix for Old Dashboard Links")
    print("=======================================")
    
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
        print("\n✅ Old dashboard links fixed successfully!")
        print("\n🔧 Links Fixed:")
        print("   - ❌ Old: /pages/dashboard/users-dashboard/users-view/users-view.php")
        print("   - ✅ New: /dashboard/users")
        print("   - ❌ Old: /pages/dashboard/comments-dashboard/comments-view/comments-view.php")
        print("   - ✅ New: /dashboard/comments")
        
        print("\n🔗 Now all navigation uses clean URLs:")
        print("   📊 Main Dashboard: https://11klassniki.ru/dashboard")
        print("   👥 Users: https://11klassniki.ru/dashboard/users")
        print("   💬 Comments: https://11klassniki.ru/dashboard/comments")
        print("   📝 Create Post: https://11klassniki.ru/create/post")
        print("   📰 Create News: https://11klassniki.ru/create/news")
        
        print("\n📋 Fixed in main dashboard:")
        print("   - Sidebar 'Пользователи' link → /dashboard/users")
        print("   - Quick action 'Все пользователи' button → /dashboard/users")
        print("   - Sidebar 'Комментарии' link → /dashboard/comments")
        print("   - Quick action 'Комментарии' button → /dashboard/comments")
        
        print("\n💡 No more 404 errors when clicking navigation links!")
        
    else:
        print("\n⚠️  Some files failed to upload. Please check the errors above.")
    
except Exception as e:
    print(f"\n❌ Error: {str(e)}")