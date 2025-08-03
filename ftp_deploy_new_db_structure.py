#!/usr/bin/env python3

import ftplib
import os
from datetime import datetime

# FTP Configuration
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

def upload_files():
    """Upload all updated files for new database structure"""
    
    print(f"📤 Deploying new database structure updates - {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    
    files_to_upload = [
        # Core includes
        '/includes/getEntityIdFromURL.php',
        
        # VPO/SPO pages
        '/pages/common/vpo-spo/single-data-fetch.php',
        '/pages/common/vpo-spo/single-content.php',
        '/pages/common/vpo-spo/generic-tabs.php',
        '/pages/common/vpo-spo/single-header-links.php',
        '/pages/common/vpo-spo/fetchNewsContent.php',
        '/pages/common/vpo-spo/send_emails.php',
        
        # Educational institutions pages
        '/pages/common/educational-institutions-all-regions/educational-institutions-all-regions.php',
        '/pages/common/educational-institutions-all-regions/educational-institutions-all-regions-content.php',
    ]
    
    success_count = 0
    failed_files = []
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd('/11klassnikiru')
        
        for file_path in files_to_upload:
            local_file = f'/Applications/XAMPP/xamppfiles/htdocs{file_path}'
            remote_path = file_path.lstrip('/')
            
            # Create directories if needed
            remote_dir = os.path.dirname(remote_path)
            if remote_dir:
                dirs = remote_dir.split('/')
                for i in range(len(dirs)):
                    dir_path = '/'.join(dirs[:i+1])
                    try:
                        ftp.cwd(f'/11klassnikiru/{dir_path}')
                    except:
                        try:
                            ftp.mkd(f'/11klassnikiru/{dir_path}')
                        except:
                            pass
            
            # Upload file
            try:
                ftp.cwd('/11klassnikiru')
                if os.path.exists(local_file):
                    with open(local_file, 'rb') as f:
                        ftp.storbinary(f'STOR {remote_path}', f)
                    print(f"✅ {file_path}")
                    success_count += 1
                else:
                    print(f"❌ {file_path} - File not found locally")
                    failed_files.append(file_path)
            except Exception as e:
                print(f"❌ {file_path} - Upload failed: {e}")
                failed_files.append(file_path)
        
        ftp.quit()
        
        print(f"\n📊 Deployment Summary:")
        print(f"✅ Successfully uploaded: {success_count}/{len(files_to_upload)} files")
        
        if failed_files:
            print(f"\n❌ Failed files:")
            for file in failed_files:
                print(f"   - {file}")
        
        print(f"\n🎯 Updated components:")
        print("- URL routing functions (getEntityIdFromURL.php)")
        print("- VPO/SPO single pages")
        print("- Educational institutions listing pages")
        print("- All table references: vpo → universities, spo → colleges")
        print("- All column references: id_vpo → id, vpo_name → university_name, etc.")
        
        print(f"\n⚠️  Important: Some pages may still show errors if there are additional files")
        print("that need updating. Monitor error logs and update as needed.")
        
    except Exception as e:
        print(f"❌ Deployment failed: {e}")

if __name__ == "__main__":
    upload_files()