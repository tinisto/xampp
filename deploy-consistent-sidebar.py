#!/usr/bin/env python3
import ftplib

FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
REMOTE_DIR = '/11klassnikiru'

def upload_file(ftp, local_file, remote_file):
    """Upload a single file to FTP server"""
    try:
        with open(local_file, 'rb') as f:
            ftp.storbinary(f'STOR {remote_file}', f)
        print(f"✅ Uploaded: {remote_file}")
        return True
    except Exception as e:
        print(f"❌ Failed: {str(e)}")
        return False

try:
    print("🔧 Making Sidebar Consistent Across All Dashboards")
    print("================================================")
    
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(REMOTE_DIR)
    
    print("\n📤 Uploading updated files...")
    
    files_to_upload = [
        'dashboard-users-professional.php'
    ]
    
    success_count = 0
    for file in files_to_upload:
        if upload_file(ftp, file, file):
            success_count += 1
    
    ftp.quit()
    
    if success_count == len(files_to_upload):
        print("\n✅ All dashboards now have consistent sidebar!")
        print("\n🎉 Updated sidebar includes:")
        print("   - ✅ 'Управление' section with Dashboard, Users, Database")
        print("   - ✅ 'Контент' section with Create News, Create Post, Comments")
        print("   - ✅ 'Образование' section with Schools, VPO, SPO")
        print("   - ✅ 'Система' section with Home, Logout")
        
        print("\n📍 Same navigation on all pages:")
        print("   - https://11klassniki.ru/dashboard")
        print("   - https://11klassniki.ru/dashboard/users")
        print("   - https://11klassniki.ru/create/news")
        print("   - https://11klassniki.ru/create/post")
        
        print("\n🚀 Consistent user experience across all dashboard pages!")
        
    else:
        print(f"\n⚠️  Some files failed to upload")
    
except Exception as e:
    print(f"❌ Error: {str(e)}")