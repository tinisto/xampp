#!/usr/bin/env python3

import ftplib

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def upload_test():
    print("🚀 Uploading region table test")
    
    try:
        print("🔌 Connecting...")
        ftp = ftplib.FTP()
        ftp.connect(HOST, 21, timeout=30)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✅ Connected")
        
        # Upload test file
        with open('/Applications/XAMPP/xamppfiles/htdocs/test-region-tables.php', 'rb') as f:
            ftp.storbinary('STOR test-region-tables.php', f)
        print("✅ Test file uploaded")
        
        ftp.quit()
        
        print("\n🎉 Check https://11klassniki.ru/test-region-tables.php")
        
        return True
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False

if __name__ == "__main__":
    upload_test()