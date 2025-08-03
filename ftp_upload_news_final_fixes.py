#!/usr/bin/env python3
"""
Upload final news fixes: 4-column grid, navigation, and category links
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
    print("🚀 Uploading final news fixes...")
    
    # FTP Configuration
    FTP_HOST = "ftp.ipage.com"
    FTP_USER = "franko"
    FTP_PASS = "JyvR!HK2E!N55Zt"
    
    # Files to upload
    files_to_upload = [
        # Fixed news main page with 4-column grid and navigation
        ('pages/common/news/news.php', 'pages/common/news/news.php'),
        # Fixed category news page with 4-column grid
        ('pages/category-news/category-news.php', 'pages/category-news/category-news.php'),
        # Fixed header with correct category links
        ('common-components/header.php', 'common-components/header.php'),
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
        print("\n📝 Final news fixes:")
        print("✅ Added 4-column responsive grid layout for news cards")
        print("✅ Added news type navigation (VPO, SPO, Schools, EGE)")
        print("✅ Fixed header categories dropdown to link to news categories")
        print("✅ Shortened card descriptions and improved typography")
        print("✅ Made cards uniform height with flexbox")
        print("\n🔍 Test the improved pages:")
        print("https://11klassniki.ru/news - Main news page with navigation")
        print("Header categories dropdown should now work properly")
            
    except Exception as e:
        print(f"❌ FTP connection failed: {str(e)}")

if __name__ == "__main__":
    main()