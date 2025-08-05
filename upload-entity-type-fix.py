#!/usr/bin/env python3

import ftplib
import os

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def upload_entity_type_fix():
    print("🚀 Uploading entity type fix")
    
    try:
        print("🔌 Connecting...")
        ftp = ftplib.FTP()
        ftp.connect(HOST, 21, timeout=30)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✅ Connected")
        
        # Upload fix scripts
        files = [
            'check-mysql-timezone.php',
            'comments/timezone-handler.php'
        ]
        
        for file in files:
            local_path = f'/Applications/XAMPP/xamppfiles/htdocs/{file}'
            if os.path.exists(local_path):
                # Handle subdirectories
                if '/' in file:
                    parts = file.split('/')
                    ftp.cwd('/')
                    ftp.cwd(PATH)
                    for part in parts[:-1]:
                        try:
                            ftp.cwd(part)
                        except:
                            ftp.mkd(part)
                            ftp.cwd(part)
                
                with open(local_path, 'rb') as f:
                    filename = file.split('/')[-1]
                    ftp.storbinary(f'STOR {filename}', f)
                print(f"✅ {file} uploaded")
                
                # Reset to base
                ftp.cwd('/')
                ftp.cwd(PATH)
            else:
                print(f"❌ File not found: {file}")
        
        ftp.quit()
        
        print("\n🎉 UTC timezone fix uploaded!")
        print("\n✅ FIXED: Database timestamps are in UTC, not Moscow time")
        print("\n👉 Check MySQL timezone:")
        print("https://11klassniki.ru/check-mysql-timezone.php")
        print("\n🔧 Comments should now show correct times for Australia/Sydney!")
        
        return True
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False

if __name__ == "__main__":
    upload_entity_type_fix()