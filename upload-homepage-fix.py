#!/usr/bin/env python3

import ftplib
import os

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def upload_homepage_fix():
    print("🚀 Uploading homepage post links fix")
    
    try:
        print("🔌 Connecting...")
        ftp = ftplib.FTP()
        ftp.connect(HOST, 21, timeout=30)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✅ Connected")
        
        # Upload fixed homepage file
        local_path = '/Applications/XAMPP/xamppfiles/htdocs/index_content_posts_with_news_style.php'
        if os.path.exists(local_path):
            with open(local_path, 'rb') as f:
                ftp.storbinary('STOR index_content_posts_with_news_style.php', f)
            print("✅ index_content_posts_with_news_style.php uploaded")
        else:
            print("❌ File not found: index_content_posts_with_news_style.php")
            return False
        
        ftp.quit()
        
        print("\n🎉 Homepage post links fix uploaded!")
        print("\n🔧 Fixed Issues:")
        print("- Changed $post['id_post'] → $post['id'] for image paths")
        print("- Changed $post['url_post'] → $post['url_slug'] for post URLs")
        print("\n🌍 Test the homepage now:")
        print("https://11klassniki.ru/")
        print("\n✅ Post links should now work correctly!")
        
        return True
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False

if __name__ == "__main__":
    upload_homepage_fix()