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
    print("🚀 Deploying Complete Content Management System")
    print("============================================")
    
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(REMOTE_DIR)
    
    print("\n📤 Uploading files...")
    
    files_to_upload = {
        'dashboard-news-management.php': 'dashboard-news-management.php',
        'dashboard-posts-management.php': 'dashboard-posts-management.php',
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
        print("\n✅ Complete content management system deployed!")
        print("\n🎉 What's new:")
        print("   - ✅ News management with sidebar closed by default")
        print("   - ✅ Posts management dashboard")
        print("   - ✅ Consistent sidebar across ALL dashboards")
        print("   - ✅ Posts management link in all sidebars")
        
        print("\n📍 Content Management:")
        print("   News: https://11klassniki.ru/dashboard/news")
        print("   Posts: https://11klassniki.ru/dashboard/posts")
        
        print("\n📝 Content Creation:")
        print("   Create News: https://11klassniki.ru/create/news")
        print("   Create Post: https://11klassniki.ru/create/post")
        
        print("\n🎯 Sidebar Features:")
        print("   - Starts closed on all management pages")
        print("   - Click ☰ to open")
        print("   - Same links everywhere")
        
        print("\n🚀 Complete content management ready!")
        
    else:
        print(f"\n⚠️  Some files failed ({success_count}/{len(files_to_upload)})")
    
except Exception as e:
    print(f"❌ Error: {str(e)}")