#!/usr/bin/env python3

import ftplib

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def upload_htaccess():
    print("🚀 Uploading .htaccess for clean URLs")
    
    try:
        print("🔌 Connecting...")
        ftp = ftplib.FTP()
        ftp.connect(HOST, 21, timeout=30)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✅ Connected")
        
        # Navigate to dashboard directory
        ftp.cwd('dashboard')
        
        # Upload .htaccess
        with open('/Applications/XAMPP/xamppfiles/htdocs/dashboard/.htaccess', 'rb') as f:
            ftp.storbinary('STOR .htaccess', f)
        print("✅ .htaccess uploaded")
        
        ftp.quit()
        
        print("\n🎉 Clean URLs enabled! Both URLs will work:")
        print("   - https://11klassniki.ru/dashboard/comments")
        print("   - https://11klassniki.ru/dashboard/comments.php")
        
        return True
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False

if __name__ == "__main__":
    upload_htaccess()