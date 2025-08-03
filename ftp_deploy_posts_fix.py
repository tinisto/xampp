#!/usr/bin/env python3

import ftplib
import os

# FTP Configuration
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

def deploy_posts_fix():
    """Deploy post URL routing debug and fix scripts"""
    
    print("📤 Deploying post URL routing fixes...")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd('/11klassnikiru')
        
        files_to_upload = [
            'debug_posts_routing.php',
            'fix_posts_urls.php'
        ]
        
        for file_path in files_to_upload:
            local_file = f'/Applications/XAMPP/xamppfiles/htdocs/{file_path}'
            
            # Upload file
            try:
                if os.path.exists(local_file):
                    with open(local_file, 'rb') as f:
                        ftp.storbinary(f'STOR {file_path}', f)
                    print(f"✅ {file_path}")
                else:
                    print(f"❌ {file_path} - File not found")
            except Exception as e:
                print(f"❌ {file_path} - Failed: {e}")
        
        ftp.quit()
        
        print("\n🎯 Post URL routing tools deployed!")
        print("\n🔍 Next steps:")
        print("1. Visit https://11klassniki.ru/debug_posts_routing.php to analyze the issue")
        print("2. Visit https://11klassniki.ru/fix_posts_urls.php to fix missing URL slugs")
        print("3. Test post URLs from the main page")
        print("\nThis should fix the 404 errors on post pages!")
        
    except Exception as e:
        print(f"❌ Deploy failed: {e}")

if __name__ == "__main__":
    deploy_posts_fix()