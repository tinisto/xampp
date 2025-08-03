#!/usr/bin/env python3

import ftplib
import os

# FTP Configuration
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

def upload_institutions_fix():
    """Deploy the fixed files to remove wrapper constraints from VPO/SPO/Schools pages"""
    
    print("🚀 Deploying Educational Institutions Wrap Fix...")
    
    # Files to upload
    files_to_upload = [
        ('common-components/content-wrapper.php', 'common-components/content-wrapper.php'),
        ('pages/common/educational-institutions-in-region/educational-institutions-in-region-content.php', 'pages/common/educational-institutions-in-region/educational-institutions-in-region-content.php')
    ]
    
    try:
        # Connect to FTP
        print(f"📡 Connecting to {FTP_HOST}...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd('/11klassnikiru')
        print("✅ Connected to FTP server")
        
        # Upload each file
        for local_path, remote_path in files_to_upload:
            full_local_path = f'/Applications/XAMPP/xamppfiles/htdocs/{local_path}'
            
            if not os.path.exists(full_local_path):
                print(f"⚠️  File not found: {local_path}")
                continue
            
            print(f"📤 Uploading {remote_path}...")
            
            # Navigate to correct directory
            ftp.cwd('/11klassnikiru')
            remote_dir = os.path.dirname(remote_path)
            if remote_dir:
                for dir_part in remote_dir.split('/'):
                    if dir_part:
                        try:
                            ftp.cwd(dir_part)
                        except ftplib.error_perm:
                            print(f"📁 Creating directory: {dir_part}")
                            ftp.mkd(dir_part)
                            ftp.cwd(dir_part)
            
            # Upload file
            with open(full_local_path, 'rb') as f:
                remote_filename = os.path.basename(remote_path)
                ftp.storbinary(f'STOR {remote_filename}', f)
                print(f"✅ Uploaded: {remote_path}")
        
        print("✅ Educational institutions wrap fix deployed!")
        print("\n🎯 Test the fixes:")
        print("1. https://11klassniki.ru/vpo-in-region/astrahanskaya-oblast")
        print("2. https://11klassniki.ru/spo-in-region/astrahanskaya-oblast") 
        print("3. https://11klassniki.ru/schools-in-region/astrahanskaya-oblast")
        
        ftp.quit()
        return True
        
    except Exception as e:
        print(f"❌ Deployment failed: {e}")
        return False

if __name__ == "__main__":
    upload_institutions_fix()