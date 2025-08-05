#!/usr/bin/env python3

import ftplib

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def upload_debug():
    print("🚀 Uploading school template debug")
    
    try:
        print("🔌 Connecting...")
        ftp = ftplib.FTP()
        ftp.connect(HOST, 21, timeout=30)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✅ Connected")
        
        # Upload debug file
        with open('/Applications/XAMPP/xamppfiles/htdocs/debug-school-template.php', 'rb') as f:
            ftp.storbinary('STOR debug-school-template.php', f)
        print("✅ Debug file uploaded")
        
        ftp.quit()
        
        print("\n🎉 Test specific scenarios:")
        print("🌐 ID-based: https://11klassniki.ru/debug-school-template.php?id_school=2718")
        print("🌐 Slug-based: https://11klassniki.ru/debug-school-template.php?url_slug=sosh-1-shimanovsk")
        print("\nThis will show exactly where the error occurs in the template.")
        
        return True
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False

if __name__ == "__main__":
    upload_debug()