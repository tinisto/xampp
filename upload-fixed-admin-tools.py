#!/usr/bin/env python3

import ftplib

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def upload_fixed_tools():
    print("🚀 Uploading fixed admin tools")
    
    try:
        print("🔌 Connecting...")
        ftp = ftplib.FTP()
        ftp.connect(HOST, 21, timeout=30)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✅ Connected")
        
        # Upload fixed cache management
        with open('/Applications/XAMPP/xamppfiles/htdocs/admin/cache-management.php', 'rb') as f:
            ftp.storbinary('STOR dashboard/cache-management.php', f)
        print("✅ Fixed cache-management.php uploaded")
        
        # Upload fixed monitoring
        with open('/Applications/XAMPP/xamppfiles/htdocs/admin/monitoring.php', 'rb') as f:
            ftp.storbinary('STOR dashboard/monitoring.php', f)
        print("✅ Fixed monitoring.php uploaded")
        
        ftp.quit()
        
        print("\n🎉 Admin tools fixed and uploaded!")
        print("\n📋 Now test the links:")
        print("🔗 https://11klassniki.ru/dashboard/cache-management.php")
        print("🔗 https://11klassniki.ru/dashboard/monitoring.php")
        
        return True
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False

if __name__ == "__main__":
    upload_fixed_tools()