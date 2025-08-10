#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import ftplib

FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def main():
    print("🗑️ DELETING EXTRA LOGO FILES")
    print("Keeping only favicon.svg")
    
    files_to_delete = [
        'images/logo.png',
        'common-components/logo.php'
    ]
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        
        deleted_count = 0
        for file_path in files_to_delete:
            try:
                ftp.delete(file_path)
                print(f"   ✅ Deleted: {file_path}")
                deleted_count += 1
            except Exception as e:
                print(f"   ❌ Could not delete {file_path}: {str(e)}")
        
        print(f"\n📊 Deleted {deleted_count} extra logo files")
        
        print(f"\n✅ NOW we have ONLY 1 logo file:")
        print(f"   • favicon.svg")
        
        ftp.quit()
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    main()