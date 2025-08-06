#!/usr/bin/env python3

import ftplib
import os
import sys

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def main():
    print("CATEGORY FULL DEBUG UPLOAD")
    print("=" * 50)
    print("🔧 Adding comprehensive debugging")
    print("   • Error reporting enabled")
    print("   • Full debug script")
    
    try:
        ftp = ftplib.FTP(HOST, USER, PASS)
        ftp.cwd(PATH)
        print("✓ Connected to FTP")
        
        # Upload updated category.php with error reporting
        with open("/Applications/XAMPP/xamppfiles/htdocs/pages/category/category.php", 'rb') as f:
            ftp.storbinary('STOR pages/category/category.php', f)
        print("✓ Uploaded: category.php (with error reporting)")
        
        # Upload full debug script
        with open("/Applications/XAMPP/xamppfiles/htdocs/pages/category/category-full-debug.php", 'rb') as f:
            ftp.storbinary('STOR pages/category/category-full-debug.php', f)
        print("✓ Uploaded: category-full-debug.php")
        
        ftp.quit()
        
        print("\n🎉 DEBUG FILES DEPLOYED!")
        
        print("\n" + "=" * 50)
        print("TEST THESE:")
        print("1. Main page (with errors shown):")
        print("   https://11klassniki.ru/category/abiturientam")
        print("\n2. Full debug:")
        print("   https://11klassniki.ru/pages/category/category-full-debug.php?category_en=abiturientam")
        print("=" * 50)
        
    except Exception as e:
        print(f"✗ Error: {str(e)}")
        return 1
    
    return 0

if __name__ == "__main__":
    sys.exit(main())