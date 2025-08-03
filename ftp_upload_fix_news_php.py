#!/usr/bin/env python3
"""
Fix news.php with debugging and error handling
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
    print("🚀 Fixing news.php with debugging...")
    
    # FTP Configuration
    FTP_HOST = "ftp.ipage.com"
    FTP_USER = "franko"
    FTP_PASS = "JyvR!HK2E!N55Zt"
    
    # Files to upload
    files_to_upload = [
        # Fixed .htaccess pointing back to news.php
        ('.htaccess', '.htaccess'),
        # Fixed news.php with debugging and error handling
        ('pages/common/news/news.php', 'pages/common/news/news.php'),
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
        print("\n📝 News.php fixes:")
        print("✅ Added error reporting and debugging")
        print("✅ Added safety checks for undefined variables")
        print("✅ Fixed .htaccess to point back to news.php")
        print("\n🔍 Test the news page:")
        print("https://11klassniki.ru/news/letnoe-uchilische-v-krasnodare-popolnitsya-15-buduschimi-voennyimi-letchitsami")
        print("\n🐛 For debugging, add ?debug=1 to see what's happening:")
        print("https://11klassniki.ru/news/letnoe-uchilische-v-krasnodare-popolnitsya-15-buduschimi-voennyimi-letchitsami?debug=1")
            
    except Exception as e:
        print(f"❌ FTP connection failed: {str(e)}")

if __name__ == "__main__":
    main()