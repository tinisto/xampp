#!/usr/bin/env python3

import ftplib

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def upload_population():
    print("🚀 Uploading school slug population script")
    
    try:
        print("🔌 Connecting...")
        ftp = ftplib.FTP()
        ftp.connect(HOST, 21, timeout=30)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✅ Connected")
        
        # Upload population script
        with open('/Applications/XAMPP/xamppfiles/htdocs/populate-school-slugs.php', 'rb') as f:
            ftp.storbinary('STOR populate-school-slugs.php', f)
        print("✅ Population script uploaded")
        
        ftp.quit()
        
        print("\n🎉 School slug population ready:")
        print("🌐 https://11klassniki.ru/populate-school-slugs.php")
        print("\nThis will:")
        print("- Generate friendly URL slugs for all schools")
        print("- Handle duplicate slugs automatically")
        print("- Show preview before execution")
        print("- Create stable URLs that won't auto-change")
        
        return True
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False

if __name__ == "__main__":
    upload_population()