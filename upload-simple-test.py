#!/usr/bin/env python3

import ftplib

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def upload_test():
    print("🚀 Uploading simple school test")
    
    try:
        print("🔌 Connecting...")
        ftp = ftplib.FTP()
        ftp.connect(HOST, 21, timeout=30)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✅ Connected")
        
        # Upload test files
        with open('/Applications/XAMPP/xamppfiles/htdocs/school-simple-test.php', 'rb') as f:
            ftp.storbinary('STOR school-simple-test.php', f)
        print("✅ Simple test uploaded")
        
        with open('/Applications/XAMPP/xamppfiles/htdocs/school-working-template.php', 'rb') as f:
            ftp.storbinary('STOR school-working-template.php', f)
        print("✅ Working school template uploaded")
        
        with open('/Applications/XAMPP/xamppfiles/htdocs/spo-vpo-working-template.php', 'rb') as f:
            ftp.storbinary('STOR spo-vpo-working-template.php', f)
        print("✅ Working SPO/VPO template uploaded")
        
        ftp.quit()
        
        print("\n🎉 Test working templates:")
        print("🌐 School: https://11klassniki.ru/school-working-template.php?id_school=2718")
        print("🌐 SPO: https://11klassniki.ru/spo-vpo-working-template.php?url_slug=belogorskiy-tehnologicheskiy-tehnikum-pischevoy-promyishlennosti&type=spo")
        print("🌐 VPO: https://11klassniki.ru/spo-vpo-working-template.php?url_slug=amijt&type=vpo")
        print("\nIf these work, we can replace the broken templates!")
        
        return True
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False

if __name__ == "__main__":
    upload_test()