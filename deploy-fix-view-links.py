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
    print("🔗 Fixing Post and News View Links")
    print("===================================")
    
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(REMOTE_DIR)
    
    print("\n📤 Uploading fixed dashboards...")
    
    files_to_upload = {
        'dashboard-news-management.php': 'dashboard-news-management.php',
        'dashboard-posts-management.php': 'dashboard-posts-management.php'
    }
    
    success_count = 0
    for local_file, remote_file in files_to_upload.items():
        if upload_file(ftp, local_file, remote_file):
            success_count += 1
    
    ftp.quit()
    
    if success_count == len(files_to_upload):
        print("\n✅ View links fixed successfully!")
        print("\n🎯 What's fixed:")
        print("   - Posts now link to /post/{url_slug} instead of /post/{id}")
        print("   - News now link to /news/{url_slug} instead of /news/{id}")
        print("   - Both use proper URL slugs from database")
        
        print("\n📋 Test the fix:")
        print("   - Posts: https://11klassniki.ru/dashboard/posts")
        print("   - News: https://11klassniki.ru/dashboard/news")
        print("   - Click 'Просмотр' to view any post or news item")
        
    else:
        print(f"\n⚠️  Some files failed ({success_count}/{len(files_to_upload)})")
    
except Exception as e:
    print(f"❌ Error: {str(e)}")