#!/usr/bin/env python3

import ftplib
import os

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def upload_to_root():
    print("🚀 Moving admin tools to root directory")
    
    try:
        print("🔌 Connecting...")
        ftp = ftplib.FTP()
        ftp.connect(HOST, 21, timeout=30)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✅ Connected")
        
        # Upload cache management to root
        cache_file = "/Applications/XAMPP/xamppfiles/htdocs/admin/cache-management.php"
        if os.path.exists(cache_file):
            with open(cache_file, 'rb') as f:
                ftp.storbinary('STOR cache-management.php', f)
            print("✅ cache-management.php uploaded to root")
        
        # Upload monitoring to root
        monitoring_file = "/Applications/XAMPP/xamppfiles/htdocs/admin/monitoring.php"
        if os.path.exists(monitoring_file):
            with open(monitoring_file, 'rb') as f:
                ftp.storbinary('STOR monitoring.php', f)
            print("✅ monitoring.php uploaded to root")
        
        # Upload migrations to root
        migrations_file = "/Applications/XAMPP/xamppfiles/htdocs/admin/run-migrations.php"
        if os.path.exists(migrations_file):
            with open(migrations_file, 'rb') as f:
                ftp.storbinary('STOR run-migrations.php', f)
            print("✅ run-migrations.php uploaded to root")
        
        ftp.quit()
        
        print("\n🎉 Admin tools now in root directory!")
        print("\n📋 Access them at:")
        print("🔗 https://11klassniki.ru/cache-management.php")
        print("🔗 https://11klassniki.ru/monitoring.php") 
        print("🔗 https://11klassniki.ru/run-migrations.php")
        
        return True
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False

if __name__ == "__main__":
    upload_to_root()