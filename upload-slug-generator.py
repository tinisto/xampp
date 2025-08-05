#!/usr/bin/env python3

import ftplib

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def upload_generator():
    print("🚀 Uploading school slug generator")
    
    try:
        print("🔌 Connecting...")
        ftp = ftplib.FTP()
        ftp.connect(HOST, 21, timeout=30)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✅ Connected")
        
        # Upload generator
        with open('/Applications/XAMPP/xamppfiles/htdocs/generate-school-slugs.php', 'rb') as f:
            ftp.storbinary('STOR generate-school-slugs.php', f)
        print("✅ Slug generator uploaded")
        
        ftp.quit()
        
        print("\n🎉 School slug generator ready:")
        print("🌐 https://11klassniki.ru/generate-school-slugs.php")
        print("\nThe generator can create slugs like:")
        print("- /school/sosh-1-shimanovsk")
        print("- /school/gimnazia-2-svobodny") 
        print("- /school/licey-3-blagoveshchensk")
        print("- /school/licey-it")
        
        return True
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False

if __name__ == "__main__":
    upload_generator()