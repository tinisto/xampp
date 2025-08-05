#!/usr/bin/env python3

import ftplib
import os

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def upload_user_timezone_fix():
    print("🚀 Uploading user timezone detection fixes")
    
    try:
        print("🔌 Connecting...")
        ftp = ftplib.FTP()
        ftp.connect(HOST, 21, timeout=30)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✅ Connected")
        
        # Files to upload
        files_to_upload = [
            ('comments/timezone-handler.php', 'comments/timezone-handler.php'),
            ('comments/comment_functions.php', 'comments/comment_functions.php'),
            ('comments/load_comments_simple.php', 'comments/load_comments_simple.php'),
            ('comments/modern-comments-component.php', 'comments/modern-comments-component.php'),
            ('test-timezone-comments.php', 'test-timezone-comments.php'),
            ('check-real-timezone.php', 'check-real-timezone.php'),
            ('test-timezone-dropdown.php', 'test-timezone-dropdown.php'),
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
        
        print(f"\n🎉 User timezone fixes uploaded! ({uploaded_count} files)")
        print("\n🔧 What's New:")
        print("- 🌍 Automatic user timezone detection")
        print("- ⏱️ Comments show in user's local time")
        print("- 🇺🇸 No more hardcoded Moscow time")
        print("- 🔄 Works for all users worldwide")
        print("\n🧪 Test page:")
        print("https://11klassniki.ru/test-timezone-comments.php")
        print("\n✅ Comments will now show correct time for each user!")
        
        return True
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False

if __name__ == "__main__":
    upload_user_timezone_fix()