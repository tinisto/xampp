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
    print("🎨 Deploying Professional Layouts")
    print("=" * 50)
    print(f"⏰ Time: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    
    # Connect to FTP
    print(f"\n📡 Connecting to {FTP_HOST}...")
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(REMOTE_DIR)
    print("✅ Connected successfully!")
    
    # Upload files
    print("\n📤 Uploading files...")
    
    files_to_upload = [
        {
            'local': 'pages/common/news/news-content.php',
            'remote': 'pages/common/news/news-content.php',
            'description': 'News content with edit/delete buttons'
        },
        {
            'local': 'pages/post/post-content-professional.php',
            'remote': 'pages/post/post-content-professional.php',
            'description': 'Professional post layout matching news'
        },
        {
            'local': 'pages/post/post.php',
            'remote': 'pages/post/post.php',
            'description': 'Updated post template to use professional layout'
        }
    ]
    
    success_count = 0
    for file_info in files_to_upload:
        print(f"\n📁 {file_info['description']}")
        if upload_file(ftp, file_info['local'], file_info['remote']):
            success_count += 1
    
    ftp.quit()
    
    if success_count == len(files_to_upload):
        print("\n✅ All files deployed successfully!")
        print("\n🌟 What's New:")
        print("   - Professional post layout matching news design")
        print("   - Removed green header wrap")
        print("   - Added Edit and Delete buttons for admins")
        print("   - Consistent typography and spacing")
        print("   - Dark mode support")
        
        print("\n🎯 Features:")
        print("   - Clean, modern design")
        print("   - Author and date information")
        print("   - View counter")
        print("   - Image support (main + additional)")
        print("   - Responsive layout")
        
    else:
        print(f"\n⚠️  Some files failed ({success_count}/{len(files_to_upload)})")
    
except Exception as e:
    print(f"❌ Error: {str(e)}")