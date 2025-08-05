#!/usr/bin/env python3

import ftplib

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def upload_debug():
    print("🚀 Uploading URL routing debug script")
    
    try:
        print("🔌 Connecting...")
        ftp = ftplib.FTP()
        ftp.connect(HOST, 21, timeout=30)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✅ Connected")
        
        # Upload debug file
        with open('/Applications/XAMPP/xamppfiles/htdocs/debug-url-routing.php', 'rb') as f:
            ftp.storbinary('STOR debug-url-routing.php', f)
        print("✅ Debug file uploaded")
        
        ftp.quit()
        
        print("\n🎉 Debug tools ready:")
        print("🌐 Main debug: https://11klassniki.ru/debug-url-routing.php")
        print("🌐 Test school: https://11klassniki.ru/debug-url-routing.php/school/2718")
        print("🌐 Test SPO: https://11klassniki.ru/debug-url-routing.php/spo/belogorskiy-tehnologicheskiy-tehnikum-pischevoy-promyishlennosti")
        print("🌐 Test VPO: https://11klassniki.ru/debug-url-routing.php/vpo/amijt")
        
        return True
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False

if __name__ == "__main__":
    upload_debug()