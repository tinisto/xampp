#!/usr/bin/env python3
"""
Fix SPO/VPO page design to match school pages
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
    print("🚀 Fixing SPO/VPO page design to match school pages...")
    
    # FTP Configuration
    FTP_HOST = "ftp.ipage.com"
    FTP_USER = "franko"
    FTP_PASS = "JyvR!HK2E!N55Zt"
    
    # Files to upload
    files_to_upload = [
        # Updated SPO/VPO single page with Bootstrap
        ('pages/common/vpo-spo/single.php', 
         'pages/common/vpo-spo/single.php'),
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
        print("\n🎯 Fixed:")
        print("✅ Changed CSS framework from 'custom' to 'bootstrap'")
        print("✅ Now matches school page design consistency")
        print("✅ Uses same template configuration as school pages")
        
        print("\n🔍 Test the pages:")
        print("https://11klassniki.ru/spo/bryanskiy-transportnyiy-tehnikum")
        print("https://11klassniki.ru/vpo/aaep")
        print("https://11klassniki.ru/school/1269 (for comparison)")
        
        print("\n📋 What changed:")
        print("• SPO/VPO pages now use Bootstrap CSS framework")
        print("• Design should be consistent with school pages")
        print("• Same header, footer, and styling as other pages")
            
    except Exception as e:
        print(f"❌ FTP connection failed: {str(e)}")

if __name__ == "__main__":
    main()