#!/usr/bin/env python3

import ftplib

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def upload_test():
    print("🚀 Uploading test connection file")
    
    try:
        print("🔌 Connecting to FTP...")
        ftp = ftplib.FTP()
        ftp.connect(HOST, 21, timeout=30)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✅ Connected to FTP")
        
        # Navigate to dashboard directory
        try:
            ftp.cwd('dashboard')
        except:
            print("Creating dashboard directory...")
            ftp.mkd('dashboard')
            ftp.cwd('dashboard')
        
        # Upload test file
        with open('/Applications/XAMPP/xamppfiles/htdocs/dashboard/test-connection.php', 'rb') as f:
            ftp.storbinary('STOR test-connection.php', f)
        print("✅ Test file uploaded")
        
        ftp.quit()
        
        print("\n🎉 Test file uploaded! Check https://11klassniki.ru/dashboard/test-connection.php")
        
        return True
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False

if __name__ == "__main__":
    upload_test()