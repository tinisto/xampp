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
    'pages/dashboard/admin-index/dashboard-content.php': 'pages/dashboard/admin-index/dashboard-content.php',
    'pages/dashboard/admin-index/dashboard.php': 'pages/dashboard/admin-index/dashboard.php',
    'common-components/template-engine-dashboard-minimal.php': 'common-components/template-engine-dashboard-minimal.php',
    'dashboard-force-new-template.php': 'dashboard-force-new-template.php'
}

try:
    print("🔧 Deploying Dashboard - Force New Template")
    print("===========================================")
    
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
        print("\n✅ Dashboard new template forced successfully!")
        print("\n🔧 Changes Applied:")
        print("   - Removed 'Send Emails to SPO' link from dashboard")
        print("   - Re-uploaded minimal dashboard template")
        print("   - Created debug dashboard file")
        print("   - Ensured new clean design without headers/footers")
        
        print("\n🔗 Test the dashboards:")
        print("Main: https://11klassniki.ru/dashboard")
        print("Debug: https://11klassniki.ru/dashboard-force-new-template.php")
        
        print("\n📋 What should work now:")
        print("   - New minimal dashboard design (dark theme, clean layout)")
        print("   - Navigation: Dashboard, Пользователи, Theme toggle, Logout")
        print("   - No 'Send Emails to SPO' link")
        print("   - Dashboard cards in clean rows")
        
        print("\n💡 If old design still shows:")
        print("   - Clear browser cache (Ctrl+F5)")
        print("   - Try the debug URL above")
        
    else:
        print("\n⚠️  Some files failed to upload. Please check the errors above.")
    
except Exception as e:
    print(f"\n❌ Error: {str(e)}")