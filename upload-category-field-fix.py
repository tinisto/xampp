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
    print("CATEGORY FIELD NAME FIX")
    print("=" * 50)
    print("🔧 Problem: Wrong field names in category queries")
    print("🎯 Solution: Changed id_category to id")
    print("   • Fixed line 28: category count query")
    print("   • Fixed line 57: posts fetch query")
    
    try:
        ftp = ftplib.FTP(HOST, USER, PASS)
        ftp.cwd(PATH)
        print("✓ Connected to FTP")
        
        # Upload fixed category content file
        with open("/Applications/XAMPP/xamppfiles/htdocs/pages/category/category-content-unified.php", 'rb') as f:
            ftp.storbinary('STOR pages/category/category-content-unified.php', f)
        print("✓ Uploaded: category-content-unified.php")
        
        ftp.quit()
        
        print("\n🎉 CATEGORY FIELD FIX DEPLOYED!")
        print("✅ Fixed database field references")
        print("✅ Category pages should now work")
        
        print("\n" + "=" * 50)
        print("TEST:")
        print("🔍 https://11klassniki.ru/category/a-naposledok-ya-skazhu")
        print("Should now display the category page properly!")
        print("=" * 50)
        
    except Exception as e:
        print(f"✗ Error: {str(e)}")
        return 1
    
    return 0

if __name__ == "__main__":
    sys.exit(main())