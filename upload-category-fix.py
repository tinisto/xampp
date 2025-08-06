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
    print("CATEGORY PAGE FIX")
    print("=" * 50)
    print("🔧 Problem: Empty screen on category pages")
    print("🎯 Solution: Remove check_under_construction.php")
    print("   • Also uploading debug version")
    
    try:
        ftp = ftplib.FTP(HOST, USER, PASS)
        ftp.cwd(PATH)
        print("✓ Connected to FTP")
        
        # Upload fixed category.php
        with open("/Applications/XAMPP/xamppfiles/htdocs/pages/category/category.php", 'rb') as f:
            ftp.storbinary('STOR pages/category/category.php', f)
        print("✓ Uploaded: category.php (fixed)")
        
        # Upload debug version
        with open("/Applications/XAMPP/xamppfiles/htdocs/pages/category/category-debug.php", 'rb') as f:
            ftp.storbinary('STOR pages/category/category-debug.php', f)
        print("✓ Uploaded: category-debug.php")
        
        ftp.quit()
        
        print("\n🎉 CATEGORY FIX DEPLOYED!")
        print("✅ Removed check_under_construction require")
        
        print("\n" + "=" * 50)
        print("TEST:")
        print("🔍 https://11klassniki.ru/category/a-naposledok-ya-skazhu")
        print("\nDEBUG:")
        print("🐛 https://11klassniki.ru/pages/category/category-debug.php?category_en=a-naposledok-ya-skazhu")
        print("=" * 50)
        
    except Exception as e:
        print(f"✗ Error: {str(e)}")
        return 1
    
    return 0

if __name__ == "__main__":
    sys.exit(main())