#!/usr/bin/env python3

import ftplib
import os

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def upload_post_comment_fixes():
    print("🚀 Uploading post comment fixes")
    
    try:
        print("🔌 Connecting...")
        ftp = ftplib.FTP()
        ftp.connect(HOST, 21, timeout=30)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✅ Connected")
        
        # Files to upload
        files_to_upload = [
            ('pages/post/post-content.php', 'pages/post/post-content.php'),
            ('check-post-comments.php', 'check-post-comments.php'),
        ]
        
        uploaded_count = 0
        
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
                            try:
                                ftp.mkd(dir_part)
                                ftp.cwd(dir_part)
                            except:
                                pass
                
                # Reset to base directory for upload
                ftp.cwd('/')
                ftp.cwd(PATH)
                if remote_dir:
                    ftp.cwd(remote_dir)
                
                with open(local_path, 'rb') as f:
                    filename = remote_file.split('/')[-1]
                    ftp.storbinary(f'STOR {filename}', f)
                print(f"✅ {remote_file} uploaded")
                uploaded_count += 1
                
                # Reset to base directory
                ftp.cwd('/')
                ftp.cwd(PATH)
            else:
                print(f"⚠️ File not found: {local_path}")
        
        ftp.quit()
        
        print(f"\n🎉 Post comment fixes uploaded! ({uploaded_count} files)")
        print("\n🔧 Fixed Issues:")
        print("- Set required $entityType and $entityId variables")
        print("- Updated to use modern comments component")
        print("- Added compatibility for both 'id' and 'id_post' fields")
        print("\n🔍 Diagnostic tool:")
        print("https://11klassniki.ru/check-post-comments.php")
        print("\n🧪 Test now:")
        print("https://11klassniki.ru/post/kogda-ege-ostalis-pozadi")
        print("\n✅ Comments should now display!")
        
        return True
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False

if __name__ == "__main__":
    upload_post_comment_fixes()