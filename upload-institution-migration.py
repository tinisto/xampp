#!/usr/bin/env python3

import ftplib

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def upload_migration():
    print("🚀 Uploading institution fields standardization migration")
    
    try:
        print("🔌 Connecting...")
        ftp = ftplib.FTP()
        ftp.connect(HOST, 21, timeout=30)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✅ Connected")
        
        # Navigate to migrations directory
        ftp.cwd('migrations')
        
        # Upload migration
        with open('/Applications/XAMPP/xamppfiles/htdocs/migrations/standardize_institutions.php', 'rb') as f:
            ftp.storbinary('STOR standardize_institutions.php', f)
        print("✅ Institution migration uploaded")
        
        ftp.quit()
        
        print("\n🎉 Institution standardization ready:")
        print("🌐 https://11klassniki.ru/migrations/standardize_institutions.php")
        print("\nThis will standardize:")
        print("- Name fields: school_name/spo_name/vpo_name → name")
        print("- Image fields: image_school_X/image_spo_X/image_vpo_X → image_X")
        print("- URL fields: Add url_slug to schools, spo_url/vpo_url → url_slug")
        print("- Add zip_code to schools table")
        
        return True
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False

if __name__ == "__main__":
    upload_migration()