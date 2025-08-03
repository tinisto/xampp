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
    'dashboard-professional.php': 'dashboard-professional.php'
}

try:
    print("🔧 Deploying Beautiful Professional Dashboard")
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
        print("\n✅ Beautiful Professional Dashboard deployed successfully!")
        print("\n🔧 Changes Applied:")
        print("   - Updated .htaccess to use dashboard-professional.php")
        print("   - Deployed the beautiful dashboard with professional design")
        
        print("\n🎨 Professional Dashboard Features:")
        print("   - ✨ Modern sidebar navigation with organized sections")
        print("   - 📊 Beautiful stats cards with icons and animations")
        print("   - 🌙 Dark/light mode toggle")
        print("   - 📱 Responsive design for mobile")
        print("   - 🎯 Quick action cards")
        print("   - 👤 Professional user menu")
        print("   - 🎨 Clean color scheme with smooth transitions")
        
        print("\n🔗 Test the new beautiful dashboard:")
        print("https://11klassniki.ru/dashboard")
        
        print("\n📋 Navigation sections include:")
        print("   - 📊 Dashboard (current)")
        print("   - 👥 Пользователи")
        print("   - 📰 Контент (News, Posts)")
        print("   - 🏫 Образование (Schools, Universities, Colleges)")
        print("   - 🏠 Главная / 🚪 Выход")
        
        print("\n💡 This is the beautiful dashboard design you saw today!")
        
    else:
        print("\n⚠️  Some files failed to upload. Please check the errors above.")
    
except Exception as e:
    print(f"\n❌ Error: {str(e)}")