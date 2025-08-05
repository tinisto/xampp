#!/usr/bin/env python3

import ftplib

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def upload_complete():
    print("🚀 Uploading complete migration")
    
    try:
        print("🔌 Connecting...")
        ftp = ftplib.FTP()
        ftp.connect(HOST, 21, timeout=30)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✅ Connected")
        
        # Navigate to migrations directory
        ftp.cwd('migrations')
        
        # Upload complete migration
        with open('/Applications/XAMPP/xamppfiles/htdocs/migrations/complete_migration.php', 'rb') as f:
            ftp.storbinary('STOR complete_migration.php', f)
        print("✅ Complete migration uploaded")
        
        ftp.quit()
        
        print("\n🎉 Complete standardization ready:")
        print("🌐 https://11klassniki.ru/migrations/complete_migration.php")
        print("\nThis will rename:")
        print("- regions.id → region_id")
        print("- towns.id → town_id") 
        print("- areas.id → area_id")
        print("- countries.id → country_id")
        
        return True
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False

if __name__ == "__main__":
    upload_complete()