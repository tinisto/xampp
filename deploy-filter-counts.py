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
    print("🚀 Deploying Filter Tab Counts")
    print("================================")
    
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(REMOTE_DIR)
    
    print("\n📤 Uploading files...")
    
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
        print("\n✅ Filter tab counts deployed successfully!")
        print("\n🎯 What's new:")
        print("   - News filters show counts: All (total), Published, Drafts")
        print("   - Posts shows total count (no filters since no status field)")
        print("   - Each filter tab displays count with colored badge")
        
        print("\n📰 News Management: https://11klassniki.ru/dashboard/news")
        print("📋 Posts Management: https://11klassniki.ru/dashboard/posts")
        
    else:
        print(f"\n⚠️  Some files failed ({success_count}/{len(files_to_upload)})")
    
except Exception as e:
    print(f"❌ Error: {str(e)}")