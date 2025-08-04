#!/usr/bin/env python3

import ftplib
import os

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def upload_dashboard():
    print("🚀 Uploading updated dashboard with admin links")
    
    try:
        print("🔌 Connecting...")
        ftp = ftplib.FTP()
        ftp.connect(HOST, 21, timeout=30)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✅ Connected")
        
        # Upload updated dashboard
        local_file = "/Applications/XAMPP/xamppfiles/htdocs/dashboard-professional.php"
        remote_file = "dashboard-professional.php"
        
        with open(local_file, 'rb') as f:
            ftp.storbinary(f'STOR {remote_file}', f)
        
        print(f"✅ {remote_file}")
        
        ftp.quit()
        
        print(f"\n🎉 Dashboard updated successfully!")
        print(f"\n📋 New admin links added to dashboard:")
        print(f"⚡ Cache Management - /admin/cache-management.php")
        print(f"📊 System Monitoring - /admin/monitoring.php")
        
        return True
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False

if __name__ == "__main__":
    upload_dashboard()