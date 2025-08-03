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
    'dashboard-create-content.php': 'dashboard-create-content.php',
    'dashboard-professional.php': 'dashboard-professional.php',
    '.htaccess': '.htaccess'
}

try:
    print("🔧 Deploying Unified Content Creation System")
    print("============================================")
    
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
        print("\n✅ Unified Content Creation System deployed successfully!")
        print("\n🔧 New Features:")
        print("   - 🎯 Single reusable component for creating posts and news")
        print("   - 🎨 Beautiful professional design matching dashboard")
        print("   - 🔗 Clean URLs instead of long file paths")
        print("   - 📱 Responsive design with sidebar toggle")
        print("   - 🎛️ Easy content type switching")
        
        print("\n🔗 New Clean URLs:")
        print("   📰 Create News: https://11klassniki.ru/create/news")
        print("   📝 Create Post: https://11klassniki.ru/create/post")
        print("   📋 General Create: https://11klassniki.ru/create")
        
        print("\n🎯 Features of the unified component:")
        print("   - Same beautiful design as other dashboards")
        print("   - Toggle between news and post creation")
        print("   - Auto-resizing textareas")
        print("   - Form validation")
        print("   - File upload for images")
        print("   - Status selection (published/draft)")
        print("   - Content type specific fields")
        
        print("\n📋 Updated dashboard links:")
        print("   - 'Создать новость' button now uses /create/news")
        print("   - 'Создать пост' button now uses /create/post")
        print("   - No more long /pages/common/news/news-create.php URLs")
        
        print("\n💡 Now both post and news creation use the same beautiful interface!")
        
    else:
        print("\n⚠️  Some files failed to upload. Please check the errors above.")
    
except Exception as e:
    print(f"\n❌ Error: {str(e)}")