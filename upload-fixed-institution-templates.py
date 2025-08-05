#!/usr/bin/env python3

import ftplib

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def upload_templates():
    print("🚀 Uploading fixed institution templates")
    
    try:
        print("🔌 Connecting...")
        ftp = ftplib.FTP()
        ftp.connect(HOST, 21, timeout=30)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✅ Connected")
        
        # Upload school template
        ftp.cwd('pages/school')
        with open('/Applications/XAMPP/xamppfiles/htdocs/pages/school/school-single-simplified.php', 'rb') as f:
            ftp.storbinary('STOR school-single-simplified.php', f)
        print("✅ School template uploaded")
        
        # Upload VPO/SPO template
        ftp.cwd('../common/vpo-spo')
        with open('/Applications/XAMPP/xamppfiles/htdocs/pages/common/vpo-spo/single-simplified.php', 'rb') as f:
            ftp.storbinary('STOR single-simplified.php', f)
        print("✅ VPO/SPO template uploaded")
        
        ftp.quit()
        
        print("\n🎉 Fixed templates deployed!")
        print("🌐 Test school: https://11klassniki.ru/school/2718")
        print("🌐 Test SPO: https://11klassniki.ru/spo/belogorskiy-tehnologicheskiy-tehnikum-pischevoy-promyishlennosti")
        print("🌐 Test VPO: https://11klassniki.ru/vpo/amijt")
        
        return True
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False

if __name__ == "__main__":
    upload_templates()