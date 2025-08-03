#!/usr/bin/env python3
"""
Upload placeholder demo fixes and admin page
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
    print("🚀 Uploading placeholder demo fixes and admin page...")
    
    # FTP Configuration
    FTP_HOST = "ftp.ipage.com"
    FTP_USER = "franko"
    FTP_PASS = "JyvR!HK2E!N55Zt"
    
    # Files to upload
    files_to_upload = [
        # API endpoint for demo
        ('api/news-content.php', 'api/news-content.php'),
        # Admin placeholders page
        ('pages/dashboard/placeholders/admin-placeholders.php', 'pages/dashboard/placeholders/admin-placeholders.php'),
    ]
    
    try:
        print(f"📡 Connecting to {FTP_HOST}...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd('/11klassnikiru')
        print("✅ Connected to FTP server")
        
        # Create api directory if it doesn't exist
        try:
            ftp.mkd('api')
            print("📁 Created /api directory")
        except:
            pass  # Directory might already exist
        
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
        
        print("\n🎯 Fixes Applied:")
        print("✅ Created API endpoint for lazy loading demo")
        print("✅ Added admin page for placeholder management")
        
        print("\n🔍 Test the fixes:")
        print("1. Demo with working content: https://11klassniki.ru/pages/common/news/news-with-placeholders.php")
        print("2. Placeholder examples: https://11klassniki.ru/common-components/placeholder-examples.php")
        print("3. Admin placeholders: https://11klassniki.ru/pages/dashboard/placeholders/admin-placeholders.php")
        
        print("\n💡 For Admins:")
        print("The admin page shows all placeholder types with:")
        print("• Live preview with theme switching")
        print("• Animation toggle")
        print("• Code examples")
        print("• Implementation guide")
        print("• Loading simulation")
            
    except Exception as e:
        print(f"❌ FTP connection failed: {str(e)}")

if __name__ == "__main__":
    main()