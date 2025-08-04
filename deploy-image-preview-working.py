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
    print("🖼️  Deploying Image Preview Feature")
    print("=" * 50)
    print(f"⏰ Time: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    
    # Connect to FTP
    print(f"\n📡 Connecting to {FTP_HOST}...")
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(REMOTE_DIR)
    print("✅ Connected successfully!")
    
    # Upload the file
    print("\n📤 Uploading dashboard-create-content-unified.php...")
    
    if upload_file(ftp, 'dashboard-create-content-unified.php', 'dashboard-create-content-unified.php'):
        print("\n✅ Image preview feature deployed successfully!")
        print("\n🌟 Features Added:")
        print("   - 📸 Real-time image preview on file selection")
        print("   - 🖼️ Support for PNG, JPG, JPEG, GIF, WebP")
        print("   - ❌ Remove button to clear selection")
        print("   - 📐 Responsive preview (max-height: 300px)")
        print("   - 🎨 Styled with rounded corners and shadow")
        
        print("\n🌐 Test the feature at:")
        print("   - Create News: https://11klassniki.ru/create/news")
        print("   - Create Post: https://11klassniki.ru/create/post")
        
    else:
        print("\n❌ Failed to deploy image preview feature")
    
    ftp.quit()
    
except Exception as e:
    print(f"❌ Error: {str(e)}")
    print("\nPlease check your internet connection and try again.")