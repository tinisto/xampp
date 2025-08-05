#!/usr/bin/env python3

import ftplib

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def upload_region_page():
    print("🚀 Uploading updated region page")
    
    try:
        print("🔌 Connecting...")
        ftp = ftplib.FTP()
        ftp.connect(HOST, 21, timeout=30)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✅ Connected")
        
        # Navigate to pages directory
        ftp.cwd('pages/common/educational-institutions-in-region')
        
        # Upload updated region page
        with open('/Applications/XAMPP/xamppfiles/htdocs/pages/common/educational-institutions-in-region/educational-institutions-in-region.php', 'rb') as f:
            ftp.storbinary('STOR educational-institutions-in-region.php', f)
        print("✅ Region page uploaded")
        
        ftp.quit()
        
        print("\n🎉 Updated region page deployed!")
        print("🌐 Test: https://11klassniki.ru/schools-in-region/amurskaya-oblast")
        print("🌐 Test: https://11klassniki.ru/vpo-in-region/amurskaya-oblast")
        print("🌐 Test: https://11klassniki.ru/spo-in-region/amurskaya-oblast")
        
        return True
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False

if __name__ == "__main__":
    upload_region_page()