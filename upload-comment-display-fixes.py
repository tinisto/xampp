#!/usr/bin/env python3

import ftplib
import os

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def upload_comment_display_fixes():
    print("🚀 Uploading comment display fixes")
    
    try:
        print("🔌 Connecting...")
        ftp = ftplib.FTP()
        ftp.connect(HOST, 21, timeout=30)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✅ Connected")
        
        # Files to upload
        files_to_upload = [
            ('comments/modern-comments-component.php', 'comments/modern-comments-component.php'),
            ('comments/load_comments.php', 'comments/load_comments.php'),
            ('comments/load_comments_simple.php', 'comments/load_comments_simple.php'),
            ('comments/display_comments.php', 'comments/display_comments.php'),
            ('comments/comment_form.php', 'comments/comment_form.php'),
            ('includes/functions/getEntityIdFromURL.php', 'includes/functions/getEntityIdFromURL.php'),
            ('debug-comments-loading.php', 'debug-comments-loading.php'),
            ('fix-comment-entity-id.php', 'fix-comment-entity-id.php'),
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
        
        print(f"\n🎉 Comment display fixes uploaded! ({uploaded_count} files)")
        print("\n🔧 Fixed Issues:")
        print("- Updated comment SELECT queries to use entity_id")
        print("- Fixed variable name compatibility (entityId vs entity_id)")
        print("- Fixed comment count query to use direct SQL")
        print("- Enhanced debug tools")
        print("\n👉 Next steps:")
        print("1. Run: https://11klassniki.ru/fix-comment-entity-id.php")
        print("2. Check: https://11klassniki.ru/debug-comments-loading.php")
        print("3. Test: https://11klassniki.ru/post/kogda-ege-ostalis-pozadi")
        print("\n✅ Comments should now display properly!")
        
        return True
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False

if __name__ == "__main__":
    upload_comment_display_fixes()