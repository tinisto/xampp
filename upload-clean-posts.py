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
    print("CLEAN POSTS META FIELDS")
    print("=" * 50)
    print("🔧 Issue: meta_k_post still exists in posts table")
    print("🎯 Solution: Remove remaining meta keyword field")
    
    try:
        ftp = ftplib.FTP(HOST, USER, PASS)
        ftp.cwd(PATH)
        print("✓ Connected to FTP")
        
        # Upload cleanup script
        with open("/Applications/XAMPP/xamppfiles/htdocs/clean-posts-meta.php", 'rb') as f:
            ftp.storbinary('STOR clean-posts-meta.php', f)
        print("✓ Uploaded: clean-posts-meta.php")
        
        ftp.quit()
        
        print("\n🔧 NEXT STEPS:")
        print("1. Run cleanup: https://11klassniki.ru/clean-posts-meta.php")
        print("2. Test category page again")
        
        print("\n✅ Database should now be fully clean!")
        print("✅ Category page should work without errors")
        
    except Exception as e:
        print(f"✗ Error: {str(e)}")
        return 1
    
    return 0

if __name__ == "__main__":
    sys.exit(main())