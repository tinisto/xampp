#!/usr/bin/env python3

import ftplib

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def upload_comments():
    print("🚀 Uploading final fixed comments dashboard")
    
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
        print("✅ Comments dashboard uploaded")
        
        # Upload fixed slug generator
        ftp.cwd('../')
        with open('/Applications/XAMPP/xamppfiles/htdocs/generate-post-slugs-fixed.php', 'rb') as f:
            ftp.storbinary('STOR generate-post-slugs-fixed.php', f)
        print("✅ Fixed slug generator uploaded")
        
        ftp.quit()
        
        print("\n🎉 Comments dashboard with URL standardization updates!")
        print("🌐 Test: https://11klassniki.ru/dashboard/comments.php")
        print("\nUpdates:")
        print("- ✅ Updated news: url_news → url_slug")  
        print("- ✅ Updated posts: url_post → url_slug")
        print("- ✅ Added school slug lookup functionality")
        print("- ✅ All entity URLs now use slug-based system")
        print("\n⚠️  Run migration: https://11klassniki.ru/migrations/standardize_url_fields.php")
        
        return True
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False

if __name__ == "__main__":
    upload_comments()