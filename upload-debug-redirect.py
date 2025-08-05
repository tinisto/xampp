#!/usr/bin/env python3

import ftplib

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def upload_debug():
    print("🚀 Uploading redirect debug script")
    
    try:
        print("🔌 Connecting...")
        ftp = ftplib.FTP()
        ftp.connect(HOST, 21, timeout=30)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✅ Connected")
        
        # Upload debug file
        with open('/Applications/XAMPP/xamppfiles/htdocs/debug-redirect.php', 'rb') as f:
            ftp.storbinary('STOR debug-redirect.php', f)
        print("✅ Debug file uploaded")
        
        ftp.quit()
        
        print("\n🎉 Debug redirect logic:")
        print("🌐 https://11klassniki.ru/debug-redirect.php?id_school=2718")
        print("\nThis will show exactly what's happening in the redirect process.")
        
        return True
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False

if __name__ == "__main__":
    upload_debug()