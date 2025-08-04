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
    print("🔧 Deploying Editor and Redirect Fixes")
    print("=" * 50)
    print(f"⏰ Time: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    
    # Connect to FTP
    print(f"\n📡 Connecting to {FTP_HOST}...")
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(REMOTE_DIR)
    print("✅ Connected successfully!")
    
    # Upload files
    print("\n📤 Uploading fixed files...")
    
    files_to_upload = [
        {
            'local': 'dashboard-create-content-unified.php',
            'remote': 'dashboard-create-content-unified.php',
            'description': 'Fixed TinyMCE line breaks'
        },
        {
            'local': 'create-process.php',
            'remote': 'create-process.php',
            'description': 'Fixed redirect and image saving'
        }
    ]
    
    success_count = 0
    for file_info in files_to_upload:
        print(f"\n📁 {file_info['description']}")
        if upload_file(ftp, file_info['local'], file_info['remote']):
            success_count += 1
    
    ftp.quit()
    
    if success_count == len(files_to_upload):
        print("\n✅ All fixes deployed successfully!")
        print("\n🌟 Fixes Applied:")
        print("   1️⃣ TinyMCE Editor:")
        print("      - Enter key now creates single line break (BR)")
        print("      - No more double spacing between lines")
        print("   2️⃣ After Creating Content:")
        print("      - Redirects to the created news/post page")
        print("      - No more staying on creation form")
        print("   3️⃣ Image Handling:")
        print("      - Images are now saved to database")
        print("      - Supports image_news and image_post columns")
        
        print("\n🌐 Test the fixes:")
        print("   - Create News: https://11klassniki.ru/create/news")
        print("   - Create Post: https://11klassniki.ru/create/post")
        
    else:
        print(f"\n⚠️  Some files failed ({success_count}/{len(files_to_upload)})")
    
except Exception as e:
    print(f"❌ Error: {str(e)}")