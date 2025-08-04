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
    'dashboard-create-content-unified.php': 'dashboard-create-content-unified.php',
    '.htaccess': '.htaccess'
}

try:
    print("🔧 Deploying Unified Sidebar Fix")
    print("================================")
    
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
        print("\n✅ Unified sidebar fix deployed successfully!")
        print("\n🔧 What's Fixed:")
        print("   - ✅ Consistent sidebar design across ALL dashboards")
        print("   - 🎨 Same look as main dashboard (11классники logo + × close button)")
        print("   - 📱 Proper mobile responsive behavior")
        print("   - 🎯 Consistent navigation structure")
        
        print("\n🔗 Test the consistent design:")
        print("Main Dashboard: https://11klassniki.ru/dashboard")
        print("Create Post: https://11klassniki.ru/create/post")
        print("Create News: https://11klassniki.ru/create/news")
        
        print("\n📋 Now all dashboards have:")
        print("   - Same sidebar header with '11классники' logo")
        print("   - Same × close button in top-right of sidebar")
        print("   - Same navigation sections and styling")
        print("   - Same responsive behavior")
        print("   - Same user menu dropdown")
        
        print("\n💡 The sidebar should now look identical everywhere!")
        
    else:
        print("\n⚠️  Some files failed to upload. Please check the errors above.")
    
except Exception as e:
    print(f"\n❌ Error: {str(e)}")