#!/usr/bin/env python3

import ftplib
import os

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def upload_complete_fix():
    print("🚀 Uploading complete comment and URL field fixes")
    
    try:
        print("🔌 Connecting...")
        ftp = ftplib.FTP()
        ftp.connect(HOST, 21, timeout=30)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✅ Connected")
        
        # Upload migration script to root
        with open('/Applications/XAMPP/xamppfiles/htdocs/complete-migration-fix.php', 'rb') as f:
            ftp.storbinary('STOR complete-migration-fix.php', f)
        print("✅ Migration script uploaded")
        
        # Upload basic URL fields migration
        with open('/Applications/XAMPP/xamppfiles/htdocs/fix-url-fields.php', 'rb') as f:
            ftp.storbinary('STOR fix-url-fields.php', f)
        print("✅ URL fields fix uploaded")
        
        # Upload all fixed files
        files_to_upload = [
            ('comments/modern-comments-component.php', 'comments/modern-comments-component.php'),
            ('pages/post/post.php', 'pages/post/post.php'),
            ('pages/common/educational-institutions-all-regions/educational-institutions-all-regions.php', 'pages/common/educational-institutions-all-regions/educational-institutions-all-regions.php'),
            ('dashboard/comments.php', 'dashboard/comments.php'),
        ]
        
        for local_file, remote_file in files_to_upload:
            local_path = f'/Applications/XAMPP/xamppfiles/htdocs/{local_file}'
            if os.path.exists(local_path):
                # Ensure directory exists
                remote_dir = '/'.join(remote_file.split('/')[:-1])
                if remote_dir:
                    try:
                        ftp.cwd('/')
                        ftp.cwd(PATH)
                        for dir_part in remote_dir.split('/'):
                            try:
                                ftp.cwd(dir_part)
                            except:
                                ftp.mkd(dir_part)
                                ftp.cwd(dir_part)
                    except:
                        pass
                
                # Reset to base directory
                ftp.cwd('/')
                ftp.cwd(PATH)
                
                # Navigate to target directory and upload
                if remote_dir:
                    ftp.cwd(remote_dir)
                
                with open(local_path, 'rb') as f:
                    filename = remote_file.split('/')[-1]
                    ftp.storbinary(f'STOR {filename}', f)
                print(f"✅ {remote_file} uploaded")
                
                # Reset to base directory
                ftp.cwd('/')
                ftp.cwd(PATH)
        
        ftp.quit()
        
        print("\n🎉 Complete fix uploaded!")
        print("\n📋 Next Steps:")
        print("1. 🔧 Run migration: https://11klassniki.ru/complete-migration-fix.php")
        print("2. 🧪 Test comments: https://11klassniki.ru/post/kogda-ege-ostalis-pozadi")
        print("3. 🔍 Check regions: https://11klassniki.ru/vpo-all-regions")
        print("\n⚡ This fixes:")
        print("- ✅ Comment submission 'invalid_id' error")
        print("- ✅ Database field name mismatches (url_post → url_slug)")
        print("- ✅ Regions table column errors (id → id_region)")
        print("- ✅ News and posts URL field standardization")
        
        return True
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False

if __name__ == "__main__":
    upload_complete_fix()