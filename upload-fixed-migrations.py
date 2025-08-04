#!/usr/bin/env python3

import ftplib

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def upload_migrations():
    print("🚀 Uploading fixed migrations file")
    
    try:
        print("🔌 Connecting...")
        ftp = ftplib.FTP()
        ftp.connect(HOST, 21, timeout=30)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✅ Connected")
        
        # Upload fixed migrations file
        with open('/Applications/XAMPP/xamppfiles/htdocs/dashboard/run-migrations.php', 'rb') as f:
            ftp.storbinary('STOR dashboard/run-migrations.php', f)
        print("✅ Fixed migrations file uploaded")
        
        ftp.quit()
        
        print("\n🎉 Fixed migrations file uploaded - no more 505 error!")
        
        return True
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False

if __name__ == "__main__":
    upload_migrations()