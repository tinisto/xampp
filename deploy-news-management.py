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
    print("🔧 Deploying News Management Dashboard")
    print("====================================")
    
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(REMOTE_DIR)
    
    print("\n📤 Uploading files...")
    
    files_to_upload = {
        'dashboard-news-management.php': 'dashboard-news-management.php',
        'create-process.php': 'create-process.php',
        '.htaccess': '.htaccess',
        'dashboard-professional.php': 'dashboard-professional.php',
        'dashboard-users-professional.php': 'dashboard-users-professional.php',
        'dashboard-create-content-unified.php': 'dashboard-create-content-unified.php'
    }
    
    success_count = 0
    for local_file, remote_file in files_to_upload.items():
        if upload_file(ftp, local_file, remote_file):
            success_count += 1
    
    ftp.quit()
    
    if success_count == len(files_to_upload):
        print("\n✅ News management system deployed!")
        print("\n🎉 What's new:")
        print("   - ✅ News management dashboard")
        print("   - ✅ Filter: All, Published, Drafts")
        print("   - ✅ View all created news")
        print("   - ✅ Russian language for success messages")
        print("   - ✅ Consistent sidebar across all dashboards")
        
        print("\n📍 News Management:")
        print("   https://11klassniki.ru/dashboard/news")
        
        print("\n🔍 Available filters:")
        print("   - All news: /dashboard/news?filter=all")
        print("   - Published: /dashboard/news?filter=published")
        print("   - Drafts: /dashboard/news?filter=draft")
        
        print("\n📝 Create content:")
        print("   - Create news: /create/news")
        print("   - Create post: /create/post")
        
        print("\n🚀 Complete content management system ready!")
        
    else:
        print(f"\n⚠️  Some files failed to upload ({success_count}/{len(files_to_upload)})")
    
except Exception as e:
    print(f"❌ Error: {str(e)}")