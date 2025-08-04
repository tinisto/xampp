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
    print("📰 Deploying News Display Fixes")
    print("=" * 50)
    print(f"⏰ Time: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    
    # Connect to FTP
    print(f"\n📡 Connecting to {FTP_HOST}...")
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(REMOTE_DIR)
    print("✅ Connected successfully!")
    
    # Upload the fixed file
    print("\n📤 Uploading news-content.php...")
    
    if upload_file(ftp, 'pages/common/news/news-content.php', 'pages/common/news/news-content.php'):
        print("\n✅ News display fixes deployed successfully!")
        print("\n🌟 What's Fixed:")
        print("   📝 Draft status indicator - shows '📝 Черновик' badge for unpublished news")
        print("   🖼️ Image display - now shows images from /uploads/content/ path")
        print("   📸 Image styling - responsive with rounded corners and shadow")
        
        print("\n📂 Image Paths:")
        print("   - Images are stored in: /uploads/content/")
        print("   - Example: /uploads/content/news_68907c4defde0.png")
        
        print("\n🌐 Test at: https://11klassniki.ru/news/dsadas")
        
    else:
        print("\n❌ Failed to deploy news display fixes")
    
    ftp.quit()
    
except Exception as e:
    print(f"❌ Error: {str(e)}")