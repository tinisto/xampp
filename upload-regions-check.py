#!/usr/bin/env python3

import ftplib

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def upload_check():
    print("🚀 Uploading regions table check")
    
    try:
        print("🔌 Connecting...")
        ftp = ftplib.FTP()
        ftp.connect(HOST, 21, timeout=30)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✅ Connected")
        
        # Upload check file
        with open('/Applications/XAMPP/xamppfiles/htdocs/check-regions-table.php', 'rb') as f:
            ftp.storbinary('STOR check-regions-table.php', f)
        print("✅ Check file uploaded")
        
        ftp.quit()
        
        print("\n🎉 Check https://11klassniki.ru/check-regions-table.php")
        
        return True
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False

if __name__ == "__main__":
    upload_check()