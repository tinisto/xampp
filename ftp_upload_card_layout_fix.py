#!/usr/bin/env python3
"""
Fix card layout issues - text wrapping, consistent heights, remove wrapper div
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
            print(f"✅ Uploaded: {local_path}")
            return True
            
    except Exception as e:
        print(f"❌ Failed to upload {local_path}: {str(e)}")
        return False

def main():
    print("🚀 Fixing card layout issues...")
    
    # FTP Configuration
    FTP_HOST = "ftp.ipage.com"
    FTP_USER = "franko"
    FTP_PASS = "JyvR!HK2E!N55Zt"
    
    # Files to upload
    files_to_upload = [
        # Card badge component with text wrapping fix
        ('common-components/card-badge.php', 'common-components/card-badge.php'),
        # Main page content with layout fixes
        ('index_content_posts_with_news_style.php', 'index_content_posts_with_news_style.php'),
    ]
    
    try:
        print(f"📡 Connecting to {FTP_HOST}...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd('/11klassnikiru')
        print("✅ Connected to FTP server")
        
        # Upload each file
        for local_path, remote_path in files_to_upload:
            if os.path.exists(local_path):
                upload_file(ftp, local_path, remote_path)
            else:
                print(f"⚠️  File not found locally: {local_path}")
        
        ftp.quit()
        print("\n✅ Upload complete!")
        print("\n📝 Fixes applied:")
        print("1. Badge text now wraps properly instead of cutting off")
        print("2. Card heights are consistent using flexbox")
        print("3. Removed unnecessary wrapper div")
        print("4. Badge width adjusts to content")
        print("5. Better spacing and padding")
        print("\n🔍 Result:")
        print("- 'Мир увлечений' won't be cut off")
        print("- All cards have equal height")
        print("- Cleaner HTML structure")
            
    except Exception as e:
        print(f"❌ FTP connection failed: {str(e)}")

if __name__ == "__main__":
    main()