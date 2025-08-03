#!/usr/bin/env python3
"""
Correct routing structure: only schools use ID, everything else uses slugs
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
    print("🚀 Correcting routing structure...")
    
    # FTP Configuration
    FTP_HOST = "ftp.ipage.com"
    FTP_USER = "franko"
    FTP_PASS = "JyvR!HK2E!N55Zt"
    
    # Files to upload
    files_to_upload = [
        # Corrected .htaccess with proper routing structure
        ('.htaccess', '.htaccess'),
        # Reverted post.php to only handle url_post
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
        print("\n📝 Corrected routing structure:")
        print("🏫 Schools: /school/{id} (ID-based)")
        print("📰 News: /news/{slug} (slug-based)")
        print("📝 Posts: /post/{slug} (slug-based)")
        print("📂 Categories: /category/{slug} (slug-based)")
        print("🎓 VPO: /vpo/{slug} (slug-based)")
        print("🏢 SPO: /spo/{slug} (slug-based)")
        print("\n🔍 Test the corrected news pages:")
        print("https://11klassniki.ru/news/letnoe-uchilische-v-krasnodare-popolnitsya-15-buduschimi-voennyimi-letchitsami")
        print("https://11klassniki.ru/news/novosti-obrazovaniya")
            
    except Exception as e:
        print(f"❌ FTP connection failed: {str(e)}")

if __name__ == "__main__":
    main()