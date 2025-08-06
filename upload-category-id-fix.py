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
    print("CATEGORY ID FIELD FIX")
    print("=" * 50)
    print("🔧 Problem: Undefined index 'id_post'")
    print("🎯 Solution: Check for both 'id' and 'id_post'")
    
    try:
        ftp = ftplib.FTP(HOST, USER, PASS)
        ftp.cwd(PATH)
        print("✓ Connected to FTP")
        
        # Upload fixed category content file
        with open("/Applications/XAMPP/xamppfiles/htdocs/pages/category/category-content-unified.php", 'rb') as f:
            ftp.storbinary('STOR pages/category/category-content-unified.php', f)
        print("✓ Uploaded: category-content-unified.php")
        
        ftp.quit()
        
        print("\n🎉 CATEGORY ID FIX DEPLOYED!")
        print("✅ Now checks for both 'id' and 'id_post'")
        print("✅ No more undefined index errors")
        
        print("\n" + "=" * 50)
        print("TEST:")
        print("🔍 https://11klassniki.ru/category/abiturientam")
        print("Should now work without errors!")
        print("=" * 50)
        
    except Exception as e:
        print(f"✗ Error: {str(e)}")
        return 1
    
    return 0

if __name__ == "__main__":
    sys.exit(main())