#!/usr/bin/env python3

import ftplib

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def upload_migration_runner():
    print("🚀 Uploading migration runner")
    
    try:
        print("🔌 Connecting...")
        ftp = ftplib.FTP()
        ftp.connect(HOST, 21, timeout=30)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✅ Connected")
        
        # Navigate to migrations directory
        ftp.cwd('migrations')
        
        # Upload migration runner
        with open('/Applications/XAMPP/xamppfiles/htdocs/migrations/run_migration.php', 'rb') as f:
            ftp.storbinary('STOR run_migration.php', f)
        print("✅ Migration runner uploaded")
        
        ftp.quit()
        
        print("\n🎉 Migration runner is ready!")
        print("🌐 Go to: https://11klassniki.ru/migrations/run_migration.php")
        print("\n⚠️  IMPORTANT:")
        print("1. This will make permanent changes to your database")
        print("2. Make sure you have a backup")
        print("3. Follow the on-screen instructions carefully")
        
        return True
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False

if __name__ == "__main__":
    upload_migration_runner()