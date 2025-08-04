#!/usr/bin/env python3

import ftplib

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def upload_comments_fix():
    print("🚀 Uploading fixed comments.php")
    
    try:
        print("🔌 Connecting...")
        ftp = ftplib.FTP()
        ftp.connect(HOST, 21, timeout=30)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✅ Connected")
        
        # Create dashboard directory if needed
        try:
            ftp.cwd('dashboard')
        except:
            ftp.mkd('dashboard')
            ftp.cwd('dashboard')
        
        # Upload fixed comments.php
        with open('/Applications/XAMPP/xamppfiles/htdocs/dashboard/comments.php', 'rb') as f:
            ftp.storbinary('STOR comments.php', f)
        print("✅ Fixed comments.php uploaded")
        
        ftp.quit()
        
        print("\n🎉 Comments page fixed! The 500 error should be resolved.")
        
        return True
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False

if __name__ == "__main__":
    upload_comments_fix()