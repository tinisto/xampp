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
    print("UPLOAD FIXED CLEANUP SCRIPT")
    print("=" * 30)
    
    try:
        ftp = ftplib.FTP(HOST, USER, PASS)
        ftp.cwd(PATH)
        
        with open("/Applications/XAMPP/xamppfiles/htdocs/clean-posts-meta-fixed.php", 'rb') as f:
            ftp.storbinary('STOR clean-posts-meta-fixed.php', f)
        print("✓ Uploaded: clean-posts-meta-fixed.php")
        
        ftp.quit()
        
        print("\nFixed MySQL syntax - try:")
        print("https://11klassniki.ru/clean-posts-meta-fixed.php")
        
    except Exception as e:
        print(f"✗ Error: {str(e)}")
        return 1
    
    return 0

if __name__ == "__main__":
    sys.exit(main())