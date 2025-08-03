#!/usr/bin/env python3
"""
Center and improve Write page layout
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
    print("🚀 Centering and improving Write page...")
    
    # FTP Configuration
    FTP_HOST = "ftp.ipage.com"
    FTP_USER = "franko"
    FTP_PASS = "JyvR!HK2E!N55Zt"
    
    # Files to upload
    files_to_upload = [
        # Updated write page to use modern template
        ('pages/write/write.php', 'pages/write/write.php'),
        # Updated write form with centered layout
        ('pages/write/write-form-modern.php', 'pages/write/write-form-modern.php'),
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
        print("\n📝 Write page improvements:")
        print("✨ Switched to modern template with proper page header")
        print("✨ Removed container wrapper for consistency")
        print("✨ Centered content with max-width: 800px")
        print("✨ Added proper padding for mobile")
        print("✨ Uses modern header and footer")
        print("\n🔍 Test the improved page at:")
        print("https://11klassniki.ru/write")
        print("Page should now be properly centered and styled")
            
    except Exception as e:
        print(f"❌ FTP connection failed: {str(e)}")

if __name__ == "__main__":
    main()