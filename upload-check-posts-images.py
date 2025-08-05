#!/usr/bin/env python3

import ftplib

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def upload_check():
    print("🚀 Uploading posts images check script")
    
    try:
        print("🔌 Connecting...")
        ftp = ftplib.FTP()
        ftp.connect(HOST, 21, timeout=30)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✅ Connected")
        
        # Upload check file
        with open('/Applications/XAMPP/xamppfiles/htdocs/check-posts-images.php', 'rb') as f:
            ftp.storbinary('STOR check-posts-images.php', f)
        print("✅ Check file uploaded")
        
        ftp.quit()
        
        print("\n🎉 Check https://11klassniki.ru/check-posts-images.php")
        
        return True
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False

if __name__ == "__main__":
    upload_check()