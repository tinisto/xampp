#!/usr/bin/env python3

import ftplib
import os

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def upload_migration_tool():
    print("🚀 Uploading migration tool")
    
    try:
        print("🔌 Connecting...")
        ftp = ftplib.FTP()
        ftp.connect(HOST, 21, timeout=30)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✅ Connected")
        
        # Upload migration tool
        local_file = "/Applications/XAMPP/xamppfiles/htdocs/admin/run-migrations.php"
        remote_file = "admin/run-migrations.php"
        
        with open(local_file, 'rb') as f:
            ftp.storbinary(f'STOR {remote_file}', f)
        
        print(f"✅ {remote_file}")
        
        ftp.quit()
        
        print(f"\n🎉 Migration tool uploaded!")
        print(f"\n📋 You can now run migrations via web interface:")
        print(f"🔗 Visit: https://11klassniki.ru/admin/run-migrations.php")
        
        return True
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False

if __name__ == "__main__":
    upload_migration_tool()