#!/usr/bin/env python3
import ftplib
from datetime import datetime

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
        print(f"❌ Failed to upload {remote_file}: {str(e)}")
        return False

try:
    print("✏️  Deploying Edit Content Functionality")
    print("=" * 50)
    print(f"⏰ Time: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    
    # Connect to FTP
    print(f"\n📡 Connecting to {FTP_HOST}...")
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(REMOTE_DIR)
    print("✅ Connected successfully!")
    
    # Upload files
    print("\n📤 Uploading edit functionality...")
    
    files_to_upload = [
        {
            'local': '.htaccess',
            'remote': '.htaccess',
            'description': 'Updated routes for edit functionality'
        },
        {
            'local': 'pages/common/news/news-header-links/news-actions.php',
            'remote': 'pages/common/news/news-header-links/news-actions.php',
            'description': 'Updated edit links to use clean URLs'
        },
        {
            'local': 'dashboard-edit-content.php',
            'remote': 'dashboard-edit-content.php',
            'description': 'Edit content page'
        },
        {
            'local': 'edit-process.php',
            'remote': 'edit-process.php',
            'description': 'Edit process handler'
        }
    ]
    
    success_count = 0
    for file_info in files_to_upload:
        print(f"\n📁 {file_info['description']}")
        if upload_file(ftp, file_info['local'], file_info['remote']):
            success_count += 1
    
    ftp.quit()
    
    if success_count == len(files_to_upload):
        print("\n✅ Edit functionality deployed successfully!")
        print("\n🌟 Features:")
        print("   ✏️ Clean edit URLs: /edit/news/{id} and /edit/post/{id}")
        print("   📝 Pre-filled form with existing content")
        print("   🖼️ Shows current image if exists")
        print("   💾 Updates content and redirects to view")
        print("   📊 Maintains status (published/draft)")
        
        print("\n🌐 How to test:")
        print("   1. Go to any news item: https://11klassniki.ru/news/{slug}")
        print("   2. Click the edit button (pencil icon)")
        print("   3. You'll be redirected to the edit form")
        print("   4. Make changes and save")
        
    else:
        print(f"\n⚠️  Some files failed ({success_count}/{len(files_to_upload)})")
    
except Exception as e:
    print(f"❌ Error: {str(e)}")