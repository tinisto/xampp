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
    print("🗑️ Deploying Delete Handlers")
    print("============================")
    
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(REMOTE_DIR)
    
    print("\n📤 Uploading delete handlers and updated dashboards...")
    
    files_to_upload = {
        'delete-news.php': 'delete-news.php',
        'delete-post.php': 'delete-post.php',
        'dashboard-news-management.php': 'dashboard-news-management.php',
        'dashboard-posts-management.php': 'dashboard-posts-management.php'
    }
    
    success_count = 0
    for local_file, remote_file in files_to_upload.items():
        if upload_file(ftp, local_file, remote_file):
            success_count += 1
    
    ftp.quit()
    
    if success_count == len(files_to_upload):
        print("\n✅ Delete functionality deployed!")
        print("\n🎯 Features:")
        print("   - Delete buttons now work for both news and posts")
        print("   - Shows success message after deletion")
        print("   - Shows error message if deletion fails")
        print("   - Redirects back to management dashboard")
        
        print("\n🗑️ Test deletion:")
        print("   - News: https://11klassniki.ru/dashboard/news")
        print("   - Posts: https://11klassniki.ru/dashboard/posts")
        print("   - Click '🗑️ Удалить' on any item")
        
    else:
        print(f"\n⚠️  Some files failed ({success_count}/{len(files_to_upload)})")
    
except Exception as e:
    print(f"❌ Error: {str(e)}")