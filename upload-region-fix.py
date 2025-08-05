#!/usr/bin/env python3

import ftplib

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def upload_fix():
    print("🚀 Uploading fixed region page")
    
    try:
        print("🔌 Connecting...")
        ftp = ftplib.FTP()
        ftp.connect(HOST, 21, timeout=30)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✅ Connected")
        
        # Navigate to the correct directory
        ftp.cwd('pages/common/educational-institutions-in-region')
        
        # Upload fixed file
        with open('/Applications/XAMPP/xamppfiles/htdocs/pages/common/educational-institutions-in-region/educational-institutions-in-region.php', 'rb') as f:
            ftp.storbinary('STOR educational-institutions-in-region.php', f)
        print("✅ Fixed region page uploaded")
        
        ftp.quit()
        
        print("\n🎉 Region pages fixed! Try:")
        print("   - https://11klassniki.ru/vpo-in-region/amurskaya-oblast")
        print("   - https://11klassniki.ru/spo-in-region/amurskaya-oblast")
        print("   - https://11klassniki.ru/schools-in-region/amurskaya-oblast")
        
        return True
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False

if __name__ == "__main__":
    upload_fix()