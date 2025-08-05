#!/usr/bin/env python3

import ftplib
import os

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def upload_comment_test():
    print("🚀 Uploading comment test file")
    
    try:
        print("🔌 Connecting...")
        ftp = ftplib.FTP()
        ftp.connect(HOST, 21, timeout=30)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✅ Connected")
        
        # Upload test file
        local_path = '/Applications/XAMPP/xamppfiles/htdocs/debug-modern-comments.php'
        if os.path.exists(local_path):
            with open(local_path, 'rb') as f:
                ftp.storbinary('STOR debug-modern-comments.php', f)
            print("✅ debug-modern-comments.php uploaded")
        else:
            print("❌ File not found")
            return False
        
        ftp.quit()
        
        print("\n🎉 Test file uploaded!")
        print("\n🔍 Check this test page:")
        print("https://11klassniki.ru/test-comments-simple.php")
        print("\nThis will show:")
        print("- All comments in database")
        print("- Post details for kogda-ege-ostalis-pozadi")
        print("- Comments for that specific post")
        print("- What getEntityIdFromURL returns")
        
        return True
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False

if __name__ == "__main__":
    upload_comment_test()