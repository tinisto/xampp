#!/usr/bin/env python3

import ftplib
import os

# FTP Configuration
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

def upload_news_fix():
    """Deploy the fixed news.php file to restore news functionality"""
    
    print("🚀 Deploying News Fix...")
    
    # Files to upload for news fix
    files_to_upload = [
        'pages/common/news/news.php',
        'debug-news-database.php',
        'fix-news-approval.php',
        'timestamp-check.php'
    ]
    
    try:
        # Connect to FTP
        print(f"📡 Connecting to {FTP_HOST}...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd('/11klassnikiru')
        print("✅ Connected to FTP server")
        
        # Upload each file
        for file_path in files_to_upload:
            local_path = f'/Applications/XAMPP/xamppfiles/htdocs/{file_path}'
            
            if not os.path.exists(local_path):
                print(f"⚠️  File not found locally: {file_path}")
                continue
            
            print(f"📤 Uploading {file_path}...")
            
            # Navigate to correct directory for upload
            try:
                ftp.cwd('/11klassnikiru')  # Reset to site root
                remote_dir = os.path.dirname(file_path)
                if remote_dir:
                    # Navigate to the target directory
                    for dir_part in remote_dir.split('/'):
                        if dir_part:
                            try:
                                ftp.cwd(dir_part)
                            except ftplib.error_perm:
                                print(f"📁 Creating directory: {dir_part}")
                                ftp.mkd(dir_part)
                                ftp.cwd(dir_part)
                
                with open(local_path, 'rb') as f:
                    remote_filename = os.path.basename(file_path)
                    ftp.storbinary(f'STOR {remote_filename}', f)
                    print(f"✅ Uploaded: {file_path}")
                    
            except Exception as e:
                print(f"❌ Failed to upload {file_path}: {e}")
        
        # Close connection
        ftp.quit()
        print("✅ News fix deployment complete!")
        print("\n🎯 Now test:")
        print("1. https://11klassniki.ru/news")
        print("2. https://11klassniki.ru/debug-news-database.php") 
        print("3. https://11klassniki.ru/fix-news-approval.php")
        
    except Exception as e:
        print(f"❌ Deployment failed: {e}")
        return False
    
    return True

if __name__ == "__main__":
    upload_news_fix()