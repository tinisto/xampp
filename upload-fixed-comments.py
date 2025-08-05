#!/usr/bin/env python3

import ftplib

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def upload_comments():
    print("🚀 Uploading fixed comments dashboard")
    
    try:
        print("🔌 Connecting...")
        ftp = ftplib.FTP()
        ftp.connect(HOST, 21, timeout=30)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✅ Connected")
        
        # Navigate to dashboard directory
        ftp.cwd('dashboard')
        
        # Upload fixed comments dashboard
        with open('/Applications/XAMPP/xamppfiles/htdocs/dashboard/comments.php', 'rb') as f:
            ftp.storbinary('STOR comments.php', f)
        print("✅ Comments dashboard fixed and uploaded")
        
        ftp.quit()
        
        print("\n🎉 Comments dashboard updated!")
        print("🌐 Test: https://11klassniki.ru/dashboard/comments.php")
        print("\nFixed issues:")
        print("- ✅ Updated vpo_url → url_slug")  
        print("- ✅ Updated spo_url → url_slug")
        print("- ✅ Compatible with database migration")
        
        return True
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False

if __name__ == "__main__":
    upload_comments()