#!/usr/bin/env python3

import ftplib

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def upload_simple():
    print("🚀 Uploading simple migration")
    
    try:
        print("🔌 Connecting...")
        ftp = ftplib.FTP()
        ftp.connect(HOST, 21, timeout=30)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✅ Connected")
        
        # Navigate to migrations directory
        ftp.cwd('migrations')
        
        # Upload simple migration
        with open('/Applications/XAMPP/xamppfiles/htdocs/migrations/simple_migration.php', 'rb') as f:
            ftp.storbinary('STOR simple_migration.php', f)
        print("✅ Simple migration uploaded")
        
        ftp.quit()
        
        print("\n🎉 Try this simpler version:")
        print("🌐 https://11klassniki.ru/migrations/simple_migration.php")
        
        return True
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False

if __name__ == "__main__":
    upload_simple()