#!/usr/bin/env python3
"""
Upload fixed news-content.php with better error handling
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
    print("🚀 Uploading fixed news-content.php...")
    
    # FTP Configuration
    FTP_HOST = "ftp.ipage.com"
    FTP_USER = "franko"
    FTP_PASS = "JyvR!HK2E!N55Zt"
    
    # Files to upload
    files_to_upload = [
        # Fixed news content with debugging
        ('pages/common/news/news-content.php', 'pages/common/news/news-content.php'),
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
        print("\n📝 News content fixes:")
        print("✅ Added robust error handling and debugging")
        print("✅ Improved dark mode CSS variable support")
        print("✅ Better empty data handling")
        print("✅ Secure prepared statements for view updates")
        print("\n🔍 Test the news page:")
        print("https://11klassniki.ru/news/letnoe-uchilische-v-krasnodare-popolnitsya-15-buduschimi-voennyimi-letchitsami")
        print("\n🐛 For content debugging, add ?content_debug=1:")
        print("https://11klassniki.ru/news/letnoe-uchilische-v-krasnodare-popolnitsya-15-buduschimi-voennyimi-letchitsami?content_debug=1")
            
    except Exception as e:
        print(f"❌ FTP connection failed: {str(e)}")

if __name__ == "__main__":
    main()