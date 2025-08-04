#!/usr/bin/env python3

import ftplib
import os

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def verify_dashboard():
    print("🔍 Verifying dashboard file on server")
    
    try:
        print("🔌 Connecting...")
        ftp = ftplib.FTP()
        ftp.connect(HOST, 21, timeout=30)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✅ Connected")
        
        # Check if file exists
        try:
            size = ftp.size('dashboard-professional.php')
            print(f"📄 dashboard-professional.php exists, size: {size} bytes")
        except:
            print("❌ dashboard-professional.php not found on server!")
            return False
        
        # Download and check content
        print("📥 Downloading current server version...")
        with open('/tmp/server-dashboard.php', 'wb') as f:
            ftp.retrbinary('RETR dashboard-professional.php', f.write)
        
        # Check if our admin links are in the file
        with open('/tmp/server-dashboard.php', 'r', encoding='utf-8') as f:
            content = f.read()
        
        has_cache_link = '/admin/cache-management.php' in content
        has_monitoring_link = '/admin/monitoring.php' in content
        has_migrations_link = '/admin/run-migrations.php' in content
        
        print(f"\n📋 Server file analysis:")
        print(f"⚡ Cache Management link: {'✅ Found' if has_cache_link else '❌ Missing'}")
        print(f"📊 System Monitoring link: {'✅ Found' if has_monitoring_link else '❌ Missing'}")
        print(f"🗄️ Migrations link: {'✅ Found' if has_migrations_link else '❌ Missing'}")
        
        if not (has_cache_link and has_monitoring_link and has_migrations_link):
            print("\n🔄 Re-uploading dashboard file...")
            local_file = "/Applications/XAMPP/xamppfiles/htdocs/dashboard-professional.php"
            
            with open(local_file, 'rb') as f:
                ftp.storbinary('STOR dashboard-professional.php', f)
            
            print("✅ Dashboard re-uploaded successfully!")
            print("\n💡 Try clearing your browser cache and refresh the page")
        else:
            print("\n✅ All admin links are present in server file")
            print("💡 Try clearing browser cache or hard refresh (Ctrl+F5)")
        
        ftp.quit()
        return True
        
    except Exception as e:
        print(f"❌ Verification failed: {e}")
        return False

if __name__ == "__main__":
    verify_dashboard()