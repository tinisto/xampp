#!/usr/bin/env python3

import ftplib
import os

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def upload_all_fixes():
    print("🚀 Uploading all bug fixes and improvements")
    
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
        print("✅ Updated dashboard-professional.php")
        
        # Upload text cleanup tool
        with open('/Applications/XAMPP/xamppfiles/htdocs/admin/database-text-cleanup.php', 'rb') as f:
            ftp.storbinary('STOR admin/database-text-cleanup.php', f)
        print("✅ database-text-cleanup.php")
        
        # Upload comments redirect
        with open('/Applications/XAMPP/xamppfiles/htdocs/dashboard/comments.php', 'rb') as f:
            ftp.storbinary('STOR dashboard/comments.php', f)
        print("✅ dashboard/comments.php")
        
        ftp.quit()
        
        print("\n🎉 All fixes uploaded successfully!")
        print("\n📋 Fixed issues:")
        print("✅ Database text cleanup utility added")
        print("✅ Comments dashboard path fixed")
        print("✅ Removed duplicate navigation links")
        print("✅ Cleaned up dashboard quick actions")
        print("✅ Added text cleanup to System section")
        
        print("\n🔗 New admin tool:")
        print("https://11klassniki.ru/admin/database-text-cleanup.php")
        
        return True
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False

if __name__ == "__main__":
    upload_all_fixes()