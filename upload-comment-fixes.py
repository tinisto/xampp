#!/usr/bin/env python3

import ftplib
import os

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def upload_comment_fixes():
    print("🚀 Uploading comment system fixes")
    
    try:
        print("🔌 Connecting...")
        ftp = ftplib.FTP()
        ftp.connect(HOST, 21, timeout=30)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✅ Connected")
        
        # Upload debug script
        with open('/Applications/XAMPP/xamppfiles/htdocs/debug-comments-loading.php', 'rb') as f:
            ftp.storbinary('STOR debug-comments-loading.php', f)
        print("✅ Comments debug script uploaded")
        
        # Upload fixed files
        files_to_upload = [
            ('includes/functions/getEntityIdFromURL.php', 'includes/functions/getEntityIdFromURL.php'),
            ('comments/modern-comments-component.php', 'comments/modern-comments-component.php'),
            ('pages/post/post.php', 'pages/post/post.php'),
            ('pages/post/post-content-professional.php', 'pages/post/post-content-professional.php'),
            ('comments/comment_form.php', 'comments/comment_form.php'),
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
        
        print("\n🎉 Comment system fixes uploaded!")
        print("\n📋 Fixed Issues:")
        print("- ✅ Database field references (url_post → url_slug)")
        print("- ✅ Missing database connection in comments component")
        print("- ✅ Post page parameter handling")
        print("- ✅ Comment form field names")
        print("\n🧪 Test comments now:")
        print("- https://11klassniki.ru/post/kuda-dvigatsya-posle-shkoly")
        print("- https://11klassniki.ru/post/kaktusy-ukhod-i-vyrashchivanie-dlya-nachinayushchikh")
        
        return True
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False

if __name__ == "__main__":
    upload_comment_fixes()