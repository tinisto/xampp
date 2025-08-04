#!/usr/bin/env python3

import ftplib

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def fix_admin_paths():
    print("🚀 Moving text cleanup tool to /dashboard/")
    
    try:
        print("🔌 Connecting...")
        ftp = ftplib.FTP()
        ftp.connect(HOST, 21, timeout=30)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✅ Connected")
        
        # Upload text cleanup to dashboard directory
        with open('/Applications/XAMPP/xamppfiles/htdocs/dashboard/database-text-cleanup.php', 'rb') as f:
            ftp.storbinary('STOR dashboard/database-text-cleanup.php', f)
        print("✅ dashboard/database-text-cleanup.php")
        
        # Upload updated dashboard with correct path
        with open('/Applications/XAMPP/xamppfiles/htdocs/dashboard-professional.php', 'rb') as f:
            ftp.storbinary('STOR dashboard-professional.php', f)
        print("✅ Updated dashboard-professional.php")
        
        ftp.quit()
        
        print("\n🎉 Admin paths fixed!")
        print("\n📋 All admin tools now in /dashboard/:")
        print("🔗 https://11klassniki.ru/dashboard/cache-management.php")
        print("🔗 https://11klassniki.ru/dashboard/monitoring.php")
        print("🔗 https://11klassniki.ru/dashboard/run-migrations.php")
        print("🔗 https://11klassniki.ru/dashboard/database-text-cleanup.php")
        print("🔗 https://11klassniki.ru/dashboard/comments.php")
        
        return True
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False

if __name__ == "__main__":
    fix_admin_paths()