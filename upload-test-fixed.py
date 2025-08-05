#!/usr/bin/env python3

import ftplib

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def upload_test():
    print("🚀 Uploading test fixed file")
    
    try:
        print("🔌 Connecting...")
        ftp = ftplib.FTP()
        ftp.connect(HOST, 21, timeout=30)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✅ Connected")
        
        # Navigate to dashboard directory
        ftp.cwd('dashboard')
        
        # Upload test file
        with open('/Applications/XAMPP/xamppfiles/htdocs/dashboard/comments-test-fixed.php', 'rb') as f:
            ftp.storbinary('STOR comments-test-fixed.php', f)
        print("✅ Test file uploaded")
        
        ftp.quit()
        
        print("\n🎉 Check https://11klassniki.ru/dashboard/comments-test-fixed.php")
        
        return True
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False

if __name__ == "__main__":
    upload_test()