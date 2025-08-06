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
    print("Uploading debug files...")
    
    try:
        ftp = ftplib.FTP(HOST, USER, PASS)
        ftp.cwd(PATH)
        
        # Upload debug files
        with open("/Applications/XAMPP/xamppfiles/htdocs/test-site-icon.php", 'rb') as f:
            ftp.storbinary('STOR test-site-icon.php', f)
        print("✓ Uploaded: test-site-icon.php")
        
        with open("/Applications/XAMPP/xamppfiles/htdocs/debug-form-template.php", 'rb') as f:
            ftp.storbinary('STOR debug-form-template.php', f)
        print("✓ Uploaded: debug-form-template.php")
        
        ftp.quit()
        
        print("\nDebug URLs:")
        print("🔍 https://11klassniki.ru/test-site-icon.php")
        print("🔍 https://11klassniki.ru/debug-form-template.php")
        
    except Exception as e:
        print(f"✗ Error: {str(e)}")
        return 1
    
    return 0

if __name__ == "__main__":
    sys.exit(main())