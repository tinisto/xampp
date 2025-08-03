#!/usr/bin/env python3
"""
Fix news routing - support both ID and URL-based routing
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
    print("🚀 Fixing news routing for empty screens...")
    
    # FTP Configuration
    FTP_HOST = "ftp.ipage.com"
    FTP_USER = "franko"
    FTP_PASS = "JyvR!HK2E!N55Zt"
    
    # Files to upload
    files_to_upload = [
        # Fixed .htaccess with proper news routing
        ('.htaccess', '.htaccess'),
        # Fixed post.php to support both id and url_post parameters
        ('pages/post/post.php', 'pages/post/post.php'),
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
        print("\n📝 News routing fixes:")
        print("✅ Added support for /news/{slug} URLs (text-based)")
        print("✅ Added support for /post/{slug} URLs (text-based)")
        print("✅ Kept existing /news/{id} and /post/{id} URLs (number-based)")
        print("✅ Fixed post.php to handle both id and url_post parameters")
        print("\n🔍 Test the fixed news pages:")
        print("https://11klassniki.ru/news/letnoe-uchilische-v-krasnodare-popolnitsya-15-buduschimi-voennyimi-letchitsami")
        print("https://11klassniki.ru/news/novosti-obrazovaniya")
        print("Should now load content instead of empty screens!")
            
    except Exception as e:
        print(f"❌ FTP connection failed: {str(e)}")

if __name__ == "__main__":
    main()