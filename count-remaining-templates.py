#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import ftplib

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def main():
    print("🔍 COUNTING REMAINING TEMPLATE FILES ON SERVER")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        # Essential files we should have
        essential_files = [
            'real_template.php',
            'common-components/real_header.php',
            'common-components/real_footer.php'
        ]
        
        found_essential = 0
        for file_path in essential_files:
            try:
                size = ftp.size(file_path)
                print(f"   ✅ {file_path} ({size} bytes)")
                found_essential += 1
            except:
                print(f"   ❌ MISSING: {file_path}")
        
        print(f"\n📊 ESSENTIAL FILES: {found_essential}/3 found")
        
        if found_essential == 3:
            print("✅ All essential template files are present!")
        else:
            print("❌ Some essential files are missing!")
        
        ftp.quit()
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()