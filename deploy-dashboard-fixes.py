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
    'pages/dashboard/users-dashboard/users-view/admin-users-content.php': 'pages/dashboard/users-dashboard/users-view/admin-users-content.php'
}

try:
    print("🔧 Deploying Dashboard Fixes")
    print("============================")
    
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
        print("\n✅ Dashboard fixes deployed successfully!")
        print("\n🔧 Issues Fixed:")
        print("   - Fixed database query error handling")
        print("   - Corrected field name mismatches (firstname → first_name)")
        print("   - Added proper error messages for debugging")
        print("   - Fixed registration_date → created_at field access")
        
        print("\n🔗 Test the fixed dashboard:")
        print("https://11klassniki.ru/pages/dashboard/users-dashboard/users-view/users-view.php")
        
        print("\n📋 What should work now:")
        print("   - Users list should load without PHP errors")
        print("   - Proper error messages if database issues persist")
        print("   - Correct field names displayed")
        
    else:
        print("\n⚠️  Some files failed to upload. Please check the errors above.")
    
except Exception as e:
    print(f"\n❌ Error: {str(e)}")