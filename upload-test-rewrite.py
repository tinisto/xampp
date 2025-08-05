#!/usr/bin/env python3

import ftplib

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def upload_test():
    print("🚀 Uploading rewrite test")
    
    try:
        print("🔌 Connecting...")
        ftp = ftplib.FTP()
        ftp.connect(HOST, 21, timeout=30)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✅ Connected")
        
        # Upload test file
        with open('/Applications/XAMPP/xamppfiles/htdocs/test-rewrite.php', 'rb') as f:
            ftp.storbinary('STOR test-rewrite.php', f)
        print("✅ Test file uploaded")
        
        ftp.quit()
        
        print("\n🎉 Test URL rewriting:")
        print("🌐 https://11klassniki.ru/test-rewrite.php")
        print("\nIf the .htaccess is working, we should also be able to add a simple rule to test it.")
        
        return True
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False

if __name__ == "__main__":
    upload_test()