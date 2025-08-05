#!/usr/bin/env python3

import ftplib

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def upload_index():
    print("🚀 Uploading dashboard index.php")
    
    try:
        print("🔌 Connecting...")
        ftp = ftplib.FTP()
        ftp.connect(HOST, 21, timeout=30)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✅ Connected")
        
        # Navigate to dashboard directory
        ftp.cwd('dashboard')
        
        # Upload index.php
        with open('/Applications/XAMPP/xamppfiles/htdocs/dashboard/index.php', 'rb') as f:
            ftp.storbinary('STOR index.php', f)
        print("✅ index.php uploaded")
        
        ftp.quit()
        
        print("\n🎉 Dashboard index uploaded!")
        print("   - https://11klassniki.ru/dashboard/ → redirects to main dashboard")
        print("   - https://11klassniki.ru/dashboard/comments.php → comments page")
        
        return True
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False

if __name__ == "__main__":
    upload_index()