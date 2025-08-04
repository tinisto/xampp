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
    print("🌙 Deploying Dark Mode to ALL Dashboard Pages")
    print("=============================================")
    
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(REMOTE_DIR)
    
    print("\n📤 Uploading all dashboard files with dark mode...")
    
    files_to_upload = {
        'dashboard-professional.php': 'dashboard-professional.php',
        'dashboard-news-management.php': 'dashboard-news-management.php',
        'dashboard-posts-management.php': 'dashboard-posts-management.php',
        'dashboard-create-content-unified.php': 'dashboard-create-content-unified.php',
        'dashboard-users-professional.php': 'dashboard-users-professional.php'
    }
    
    success_count = 0
    for local_file, remote_file in files_to_upload.items():
        if upload_file(ftp, local_file, remote_file):
            success_count += 1
    
    ftp.quit()
    
    if success_count == len(files_to_upload):
        print("\n✅ Dark mode deployed to ALL dashboard pages!")
        print("\n🌟 Dark Mode Features:")
        print("   - 🌞/🌙 Toggle button on every dashboard page")
        print("   - Synchronized across all pages (saves preference)")
        print("   - Beautiful dark theme with proper contrast")
        print("   - Smooth transitions between themes")
        
        print("\n📱 Test Dark Mode On:")
        print("   - Dashboard: https://11klassniki.ru/dashboard")
        print("   - Users: https://11klassniki.ru/dashboard/users")
        print("   - News: https://11klassniki.ru/dashboard/news")
        print("   - Posts: https://11klassniki.ru/dashboard/posts")
        print("   - Create: https://11klassniki.ru/create/news")
        
    else:
        print(f"\n⚠️  Some files failed ({success_count}/{len(files_to_upload)})")
    
except Exception as e:
    print(f"❌ Error: {str(e)}")