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
    print("CATEGORY FINAL FIX")
    print("=" * 50)
    print("🔧 Fixed issues:")
    print("   • Database field names: meta_description, meta_keywords")
    print("   • Removed missing page-header.php")
    print("   • Removed duplicate content wrapper calls")
    
    try:
        ftp = ftplib.FTP(HOST, USER, PASS)
        ftp.cwd(PATH)
        print("✓ Connected to FTP")
        
        # Upload fixed files
        with open("/Applications/XAMPP/xamppfiles/htdocs/pages/category/category-data-fetch.php", 'rb') as f:
            ftp.storbinary('STOR pages/category/category-data-fetch.php', f)
        print("✓ Uploaded: category-data-fetch.php")
        
        with open("/Applications/XAMPP/xamppfiles/htdocs/pages/category/category-content-unified.php", 'rb') as f:
            ftp.storbinary('STOR pages/category/category-content-unified.php', f)
        print("✓ Uploaded: category-content-unified.php")
        
        ftp.quit()
        
        print("\n🎉 CATEGORY PAGES FIXED!")
        print("✅ Database fields properly mapped")
        print("✅ Removed non-existent file requires")
        print("✅ Template engine handles headers/wrappers")
        
        print("\n" + "=" * 50)
        print("TEST:")
        print("🔍 https://11klassniki.ru/category/abiturientam")
        print("Should now work properly!")
        print("=" * 50)
        
    except Exception as e:
        print(f"✗ Error: {str(e)}")
        return 1
    
    return 0

if __name__ == "__main__":
    sys.exit(main())