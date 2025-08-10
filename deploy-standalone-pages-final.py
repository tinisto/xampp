#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import ftplib
import os

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def main():
    print("📤 Deploying standalone pages to production server...")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # List of standalone pages to upload
        pages = [
            'login-standalone.php',
            'registration-standalone.php', 
            'privacy-standalone.php',
            'forgot-password-standalone.php'
        ]
        
        uploaded = 0
        
        for page in pages:
            filepath = f"/Applications/XAMPP/xamppfiles/htdocs/{page}"
            if os.path.exists(filepath):
                print(f"📤 Uploading {page}...")
                with open(filepath, 'rb') as file:
                    ftp.storbinary(f'STOR /{page}', file)
                uploaded += 1
                print(f"✅ Uploaded {page}")
            else:
                print(f"⚠️  File not found: {page}")
        
        ftp.quit()
        
        print(f"\n✅ Successfully uploaded {uploaded} standalone pages!")
        print("\n🧪 Test URLs:")
        print("• https://11klassniki.ru/login/")
        print("• https://11klassniki.ru/registration/")
        print("• https://11klassniki.ru/privacy/")
        print("• https://11klassniki.ru/forgot-password/")
        
    except Exception as e:
        print(f"❌ FTP Error: {str(e)}")

if __name__ == "__main__":
    main()