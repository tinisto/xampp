#!/usr/bin/env python3

import ftplib
import os

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def move_to_dashboard():
    print("🚀 Moving admin tools to /dashboard directory")
    
    try:
        print("🔌 Connecting...")
        ftp = ftplib.FTP()
        ftp.connect(HOST, 21, timeout=30)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✅ Connected")
        
        # Create dashboard directory if it doesn't exist
        try:
            ftp.mkd('dashboard')
            print("📁 Created dashboard directory")
        except:
            print("📁 Dashboard directory already exists")
        
        # Upload cache management to dashboard
        cache_file = "/Applications/XAMPP/xamppfiles/htdocs/admin/cache-management.php"
        if os.path.exists(cache_file):
            with open(cache_file, 'rb') as f:
                ftp.storbinary('STOR dashboard/cache-management.php', f)
            print("✅ dashboard/cache-management.php uploaded")
        
        # Upload monitoring to dashboard
        monitoring_file = "/Applications/XAMPP/xamppfiles/htdocs/admin/monitoring.php"
        if os.path.exists(monitoring_file):
            with open(monitoring_file, 'rb') as f:
                ftp.storbinary('STOR dashboard/monitoring.php', f)
            print("✅ dashboard/monitoring.php uploaded")
        
        # Upload migrations to dashboard
        migrations_file = "/Applications/XAMPP/xamppfiles/htdocs/admin/run-migrations.php"
        if os.path.exists(migrations_file):
            with open(migrations_file, 'rb') as f:
                ftp.storbinary('STOR dashboard/run-migrations.php', f)
            print("✅ dashboard/run-migrations.php uploaded")
        
        ftp.quit()
        
        print("\n🎉 Admin tools moved to /dashboard!")
        print("\n📋 Now accessible at:")
        print("🔗 https://11klassniki.ru/dashboard/cache-management.php")
        print("🔗 https://11klassniki.ru/dashboard/monitoring.php") 
        print("🔗 https://11klassniki.ru/dashboard/run-migrations.php")
        
        return True
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False

if __name__ == "__main__":
    move_to_dashboard()