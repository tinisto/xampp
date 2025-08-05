#!/usr/bin/env python3

import ftplib

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def upload_templates():
    print("🚀 Uploading standardized templates")
    
    try:
        print("🔌 Connecting...")
        ftp = ftplib.FTP()
        ftp.connect(HOST, 21, timeout=30)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✅ Connected")
        
        # Upload .htaccess
        with open('/Applications/XAMPP/xamppfiles/htdocs/.htaccess', 'rb') as f:
            ftp.storbinary('STOR .htaccess', f)
        print("✅ .htaccess uploaded (updated URL routing)")
        
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
        
        # Upload region template
        ftp.cwd('../educational-institutions-in-region')
        with open('/Applications/XAMPP/xamppfiles/htdocs/pages/common/educational-institutions-in-region/educational-institutions-in-region.php', 'rb') as f:
            ftp.storbinary('STOR educational-institutions-in-region.php', f)
        print("✅ Region template uploaded")
        
        ftp.quit()
        
        print("\n🎉 All standardized templates deployed!")
        print("\nUpdated templates with standardized field names:")
        print("- ✅ Schools: school_name → name, image_school_X → image_X")
        print("- ✅ SPO: spo_name → name, image_spo_X → image_X, spo_url → url_slug")
        print("- ✅ VPO: vpo_name → name, image_vpo_X → image_X, vpo_url → url_slug")
        print("- ✅ URL routing: Both slug and ID-based URLs supported")
        
        print("\nTest URLs:")
        print("🌐 Schools (slug): https://11klassniki.ru/school/sosh-1-shimanovsk")
        print("🌐 Schools (ID): https://11klassniki.ru/school/2718")
        print("🌐 SPO: https://11klassniki.ru/spo/belogorskiy-tehnologicheskiy-tehnikum-pischevoy-promyishlennosti")
        print("🌐 VPO: https://11klassniki.ru/vpo/amijt")
        
        return True
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False

if __name__ == "__main__":
    upload_templates()