#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import ftplib

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def main():
    print("🔍 CHECKING ACTUAL LOGO FILES")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Check actual logo files (not logout files)
        print("\n📊 REAL LOGO/FAVICON FILES:")
        
        logo_files = []
        
        # Check specific files
        files_to_check = [
            'favicon.svg',
            'favicon.ico',
            'favicon.png',
            'logo.svg',
            'logo.png',
            'images/logo.png',
            'images/logo.svg',
            'common-components/logo.php'
        ]
        
        for file_path in files_to_check:
            try:
                size = ftp.size(file_path)
                print(f"   ✅ FOUND: {file_path} ({size} bytes)")
                logo_files.append(file_path)
            except:
                # File doesn't exist
                pass
        
        print(f"\n📊 ACTUAL logo files found: {len(logo_files)}")
        
        if len(logo_files) > 1:
            print(f"\n🗑️ Extra files to delete:")
            for f in logo_files:
                if f != 'favicon.svg':  # Keep only favicon.svg
                    print(f"   • {f}")
        
        ftp.quit()
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()