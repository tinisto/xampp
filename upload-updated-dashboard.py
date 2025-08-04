#!/usr/bin/env python3

import ftplib

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def upload_dashboard():
    print("🚀 Uploading updated dashboard")
    
    try:
        print("🔌 Connecting...")
        ftp = ftplib.FTP()
        ftp.connect(HOST, 21, timeout=30)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✅ Connected")
        
        # Upload updated dashboard
        with open('/Applications/XAMPP/xamppfiles/htdocs/dashboard-professional.php', 'rb') as f:
            ftp.storbinary('STOR dashboard-professional.php', f)
        print("✅ Updated dashboard uploaded")
        
        ftp.quit()
        
        print("\n🎉 Dashboard updated with /dashboard/ links!")
        
        return True
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False

if __name__ == "__main__":
    upload_dashboard()