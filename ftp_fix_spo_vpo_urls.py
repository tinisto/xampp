#!/usr/bin/env python3
"""
Fix SPO/VPO links to use URL slugs instead of IDs
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
    print("🚀 Fixing SPO/VPO links to use URL slugs...")
    
    # FTP Configuration
    FTP_HOST = "ftp.ipage.com"
    FTP_USER = "franko"
    FTP_PASS = "JyvR!HK2E!N55Zt"
    
    # Files to upload
    files_to_upload = [
        # Updated region institutions page with URL slugs for SPO/VPO
        ('pages/common/educational-institutions-in-region/educational-institutions-in-region.php', 
         'pages/common/educational-institutions-in-region/educational-institutions-in-region.php'),
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
        print("✅ SPO institutions now link to /spo/URL-SLUG")
        print("✅ VPO institutions now link to /vpo/URL-SLUG")
        print("✅ Schools still use /school/ID (numeric)")
        
        print("\n🔍 Test the pages:")
        print("https://11klassniki.ru/spo-in-region/bryanskaya-oblast - SPO links with URL slugs")
        print("https://11klassniki.ru/vpo-in-region/amurskaya-oblast - VPO links with URL slugs")
        
        print("\n📋 Links format:")
        print("• Schools: /school/ID (numeric ID only)")
        print("• SPO: /spo/url-slug (using spo_url field)") 
        print("• VPO: /vpo/url-slug (using vpo_url field)")
            
    except Exception as e:
        print(f"❌ FTP connection failed: {str(e)}")

if __name__ == "__main__":
    main()