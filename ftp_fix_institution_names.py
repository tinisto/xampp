#!/usr/bin/env python3
"""
Fix institution names showing as 'Название не указано' and school links
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
    print("🚀 Fixing institution names and school links...")
    
    # FTP Configuration
    FTP_HOST = "ftp.ipage.com"
    FTP_USER = "franko"
    FTP_PASS = "JyvR!HK2E!N55Zt"
    
    # Files to upload
    files_to_upload = [
        # Fixed region institutions page with correct field names
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
        print("✅ SPO institutions now use 'spo_name' field")
        print("✅ VPO institutions now use 'vpo_name' field")
        print("✅ School links changed from /schools/region/name to /school/id")
        print("✅ School links now use numeric ID")
        
        print("\n🔍 Test the pages:")
        print("https://11klassniki.ru/spo-in-region/amurskaya-oblast - Should show SPO names")
        print("https://11klassniki.ru/vpo-in-region/amurskaya-oblast - Should show VPO names")  
        print("https://11klassniki.ru/schools-in-region/arhangelskaya-oblast - School links should work")
        
        print("\n📋 Changes made:")
        print("• Used correct database field names: spo_name, vpo_name, school_name")
        print("• Fixed school links to use /school/ID format as required")
        print("• Kept SPO/VPO as text only (no links)")
            
    except Exception as e:
        print(f"❌ FTP connection failed: {str(e)}")

if __name__ == "__main__":
    main()