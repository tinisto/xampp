#!/usr/bin/env python3

import ftplib
import os

# FTP Configuration
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

def upload_post_fix():
    """Deploy the fixed post-content.php file to remove wrap div"""
    
    print("🚀 Deploying Post Wrap Fix...")
    
    # File to upload
    local_path = '/Applications/XAMPP/xamppfiles/htdocs/pages/post/post-content.php'
    remote_path = 'pages/post/post-content.php'
    
    if not os.path.exists(local_path):
        print(f"❌ File not found: {local_path}")
        return False
    
    try:
        # Connect to FTP
        print(f"📡 Connecting to {FTP_HOST}...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd('/11klassnikiru')
        print("✅ Connected to FTP server")
        
        # Navigate to pages/post directory
        ftp.cwd('pages')
        ftp.cwd('post')
        
        # Upload file
        print(f"📤 Uploading {remote_path}...")
        with open(local_path, 'rb') as f:
            ftp.storbinary('STOR post-content.php', f)
        
        print("✅ Post wrap fix deployed!")
        print("\n🎯 Test the fix:")
        print("https://11klassniki.ru/post/obrazovanie-za-rubezhom-shag-v-budushchee")
        
        ftp.quit()
        return True
        
    except Exception as e:
        print(f"❌ Deployment failed: {e}")
        return False

if __name__ == "__main__":
    upload_post_fix()