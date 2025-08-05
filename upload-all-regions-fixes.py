#!/usr/bin/env python3

import ftplib
import os

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def upload_all_regions_fixes():
    print("🚀 Uploading all-regions pages fixes")
    
    try:
        print("🔌 Connecting...")
        ftp = ftplib.FTP()
        ftp.connect(HOST, 21, timeout=30)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✅ Connected")
        
        # Upload fixed files
        files_to_upload = [
            ('find-and-replace-old-fields.php', 'find-and-replace-old-fields.php'),
            ('migrations/prioritized_field_migration.php', 'migrations/prioritized_field_migration.php'),
        ]
        
        for local_file, remote_file in files_to_upload:
            local_path = f'/Applications/XAMPP/xamppfiles/htdocs/{local_file}'
            if os.path.exists(local_path):
                # Navigate to target directory
                remote_dir = '/'.join(remote_file.split('/')[:-1])
                if remote_dir:
                    # Reset to base and navigate to target
                    ftp.cwd('/')
                    ftp.cwd(PATH)
                    for dir_part in remote_dir.split('/'):
                        try:
                            ftp.cwd(dir_part)
                        except:
                            ftp.mkd(dir_part)
                            ftp.cwd(dir_part)
                
                # Reset to base directory for upload
                ftp.cwd('/')
                ftp.cwd(PATH)
                if remote_dir:
                    ftp.cwd(remote_dir)
                
                with open(local_path, 'rb') as f:
                    filename = remote_file.split('/')[-1]
                    ftp.storbinary(f'STOR {filename}', f)
                print(f"✅ {remote_file} uploaded")
                
                # Reset to base directory
                ftp.cwd('/')
                ftp.cwd(PATH)
            else:
                print(f"⚠️ File not found: {local_path}")
        
        ftp.quit()
        
        print("\n🎉 All-regions pages fixes uploaded!")
        print("\n📋 Fixed Issues:")
        print("- ✅ Changed id_region → id in regions table queries")
        print("- ✅ Changed id_region → region_id for foreign keys")
        print("- ✅ Changed id_school → id for schools table")
        print("- ✅ Updated all data attributes and references")
        print("\n🧪 Test the pages now:")
        print("- https://11klassniki.ru/schools-all-regions")
        print("- https://11klassniki.ru/vpo-all-regions")
        print("- https://11klassniki.ru/spo-all-regions")
        
        return True
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False

if __name__ == "__main__":
    upload_all_regions_fixes()