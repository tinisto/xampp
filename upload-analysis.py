#!/usr/bin/env python3

import ftplib

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def upload_analysis():
    print("🚀 Uploading field analysis")
    
    try:
        print("🔌 Connecting...")
        ftp = ftplib.FTP()
        ftp.connect(HOST, 21, timeout=30)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✅ Connected")
        
        # Upload analysis file
        with open('/Applications/XAMPP/xamppfiles/htdocs/analyze-field-inconsistencies.php', 'rb') as f:
            ftp.storbinary('STOR analyze-field-inconsistencies.php', f)
        print("✅ Analysis file uploaded")
        
        ftp.quit()
        
        print("\n🎉 Check https://11klassniki.ru/analyze-field-inconsistencies.php")
        
        return True
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False

if __name__ == "__main__":
    upload_analysis()