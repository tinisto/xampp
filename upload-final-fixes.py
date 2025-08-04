#!/usr/bin/env python3

import ftplib

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def upload_final_fixes():
    print("🚀 Uploading final UI fixes")
    
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
        
        # Upload new comments management
        with open('/Applications/XAMPP/xamppfiles/htdocs/dashboard/comments.php', 'rb') as f:
            ftp.storbinary('STOR dashboard/comments.php', f)
        print("✅ New dashboard/comments.php")
        
        ftp.quit()
        
        print("\n🎉 All UI fixes uploaded!")
        print("\n📋 Fixed issues:")
        print("✅ Removed dropdown arrow from user menu")
        print("✅ Removed Home link from user dropdown")
        print("✅ Added + icon to 'Create post' like News")
        print("✅ Fixed comments dashboard with proper functionality")
        print("✅ Comments now work without template engine errors")
        
        print("\n🔗 Comments dashboard:")
        print("https://11klassniki.ru/dashboard/comments.php")
        
        return True
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False

if __name__ == "__main__":
    upload_final_fixes()