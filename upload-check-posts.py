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
    print("Uploading posts field checker...")
    
    try:
        ftp = ftplib.FTP(HOST, USER, PASS)
        ftp.cwd(PATH)
        
        with open("/Applications/XAMPP/xamppfiles/htdocs/check-posts-fields.php", 'rb') as f:
            ftp.storbinary('STOR check-posts-fields.php', f)
        print("✓ Uploaded: check-posts-fields.php")
        
        ftp.quit()
        
        print("\nCheck: https://11klassniki.ru/check-posts-fields.php")
        
    except Exception as e:
        print(f"✗ Error: {str(e)}")
        return 1
    
    return 0

if __name__ == "__main__":
    sys.exit(main())