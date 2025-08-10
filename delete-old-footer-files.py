#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import ftplib

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def main():
    print("🗑️ DELETING OLD FOOTER FILES")
    
    old_files = [
        'common-components/footer-modern.php.old',
        'common-components/footer.php.old'
    ]
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        for file_path in old_files:
            try:
                ftp.delete(file_path)
                print(f"   ✅ Deleted: {file_path}")
            except:
                print(f"   ⚪ Not found: {file_path}")
        
        ftp.quit()
        
        print(f"\n✅ OLD FOOTER FILES CLEANED UP!")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()