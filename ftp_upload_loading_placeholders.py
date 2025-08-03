#!/usr/bin/env python3
"""
Upload context-aware loading placeholders system
"""

import ftplib
import os

def upload_file(ftp, local_path, remote_path):
    """Upload a single file to FTP server"""
    try:
        # Navigate to remote directory
        remote_dir = os.path.dirname(remote_path)
        ftp.cwd('/11klassnikiru')
        
        if remote_dir:
            dirs = remote_dir.split('/')
            for dir in dirs:
                if dir:
                    try:
                        ftp.cwd(dir)
                    except:
                        ftp.mkd(dir)
                        ftp.cwd(dir)
        
        # Upload file
        with open(local_path, 'rb') as file:
            filename = os.path.basename(remote_path)
            ftp.storbinary(f'STOR {filename}', file)
            print(f"✅ Uploaded: {remote_path}")
            return True
            
    except Exception as e:
        print(f"❌ Failed to upload {local_path}: {str(e)}")
        return False

def main():
    print("🚀 Uploading context-aware loading placeholders...")
    
    # FTP Configuration
    FTP_HOST = "ftp.ipage.com"
    FTP_USER = "franko"
    FTP_PASS = "JyvR!HK2E!N55Zt"
    
    # Files to upload
    files_to_upload = [
        # Core placeholder component
        ('common-components/loading-placeholders-v2.php', 'common-components/loading-placeholders-v2.php'),
        # JavaScript lazy loader
        ('js/lazy-content-loader.js', 'js/lazy-content-loader.js'),
        # Example pages
        ('common-components/placeholder-examples.php', 'common-components/placeholder-examples.php'),
        ('pages/common/news/news-with-placeholders.php', 'pages/common/news/news-with-placeholders.php'),
    ]
    
    try:
        print(f"📡 Connecting to {FTP_HOST}...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd('/11klassnikiru')
        print("✅ Connected to FTP server")
        
        # Upload each file
        uploaded = 0
        for local_path, remote_path in files_to_upload:
            if os.path.exists(local_path):
                if upload_file(ftp, local_path, remote_path):
                    uploaded += 1
            else:
                print(f"⚠️  File not found locally: {local_path}")
        
        ftp.quit()
        print(f"\n✅ Upload complete! {uploaded}/{len(files_to_upload)} files uploaded")
        
        print("\n🎯 Context-Aware Loading Placeholders Implemented:")
        print("✅ News card placeholders - match actual news card structure")
        print("✅ Post card placeholders - with image areas and badges")
        print("✅ School/institution placeholders - with contact info layout")
        print("✅ Test card placeholders - centered with icon space")
        print("✅ Category header placeholders - title with post count")
        print("✅ Article content placeholders - full article skeleton")
        print("✅ Comment placeholders - with avatar and text")
        print("✅ Table row placeholders - for data tables")
        
        print("\n📋 Features:")
        print("• Responsive grid layouts")
        print("• Dark mode support")
        print("• Smooth skeleton animations")
        print("• Lazy loading with intersection observer")
        print("• Error states and retry functionality")
        
        print("\n🔍 View Examples:")
        print("https://11klassniki.ru/common-components/placeholder-examples.php")
        print("https://11klassniki.ru/pages/common/news/news-with-placeholders.php")
        
        print("\n💡 Usage in PHP:")
        print('<?php renderPlaceholderGrid("news-card", 12, 4); ?>')
        print('<?php renderContextAwarePlaceholder("article-content"); ?>')
            
    except Exception as e:
        print(f"❌ FTP connection failed: {str(e)}")

if __name__ == "__main__":
    main()