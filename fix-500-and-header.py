#!/usr/bin/env python3

import ftplib

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def fix_500_and_header():
    print("🚀 Fixing 500 error and header layout")
    
    try:
        print("🔌 Connecting...")
        ftp = ftplib.FTP()
        ftp.connect(HOST, 21, timeout=30)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✅ Connected")
        
        # Upload fixed comments page
        with open('/Applications/XAMPP/xamppfiles/htdocs/dashboard/comments.php', 'rb') as f:
            ftp.storbinary('STOR dashboard/comments.php', f)
        print("✅ Fixed dashboard/comments.php (500 error resolved)")
        
        # Upload updated dashboard with header fix
        with open('/Applications/XAMPP/xamppfiles/htdocs/dashboard-professional.php', 'rb') as f:
            ftp.storbinary('STOR dashboard-professional.php', f)
        print("✅ Updated dashboard-professional.php (header wrapper removed)")
        
        ftp.quit()
        
        print("\n🎉 Fixes uploaded successfully!")
        print("\n📋 Fixed issues:")
        print("✅ Comments page 500 error - added missing config loader")
        print("✅ Header layout - removed wrapper div from theme toggle and user menu")
        
        print("\n🔗 Test the comments page:")
        print("https://11klassniki.ru/dashboard/comments.php")
        
        return True
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False

if __name__ == "__main__":
    fix_500_and_header()