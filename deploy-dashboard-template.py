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
    'common-components/template-engine-dashboard-minimal.php': 'common-components/template-engine-dashboard-minimal.php',
    'pages/dashboard/admin-index/dashboard.php': 'pages/dashboard/admin-index/dashboard.php',
    'pages/dashboard/users-dashboard/users-view/users-view.php': 'pages/dashboard/users-dashboard/users-view/users-view.php'
}

try:
    print("🔧 Deploying Dashboard Template Updates")
    print("======================================")
    
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
        print("\n✅ Dashboard template updates deployed successfully!")
        print("\n🔧 Changes Deployed:")
        print("   - New minimal dashboard template (no header/footer)")
        print("   - Updated main dashboard to use minimal template")
        print("   - Updated users dashboard to use minimal template")
        print("   - Fixed navigation text to Russian (Пользователи)")
        
        print("\n🔗 Test the updated dashboards:")
        print("https://11klassniki.ru/dashboard")
        print("https://11klassniki.ru/dashboard/users")
        
        print("\n📋 What should work now:")
        print("   - Clean dashboard layout without headers/footers")
        print("   - Proper navigation between dashboard pages")
        print("   - No more footer.php warnings")
        print("   - Theme toggle functionality")
        
    else:
        print("\n⚠️  Some files failed to upload. Please check the errors above.")
    
except Exception as e:
    print(f"\n❌ Error: {str(e)}")