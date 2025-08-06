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
    try:
        ftp = ftplib.FTP(HOST, USER, PASS)
        ftp.cwd(PATH)
        
        with open("/Applications/XAMPP/xamppfiles/htdocs/test-login-icon.php", 'rb') as f:
            ftp.storbinary('STOR test-login-icon.php', f)
        print("✓ Uploaded: test-login-icon.php")
        
        ftp.quit()
        
        print("\nTest: https://11klassniki.ru/test-login-icon.php")
        print("This will show what the form template actually produces")
        
    except Exception as e:
        print(f"✗ Error: {str(e)}")
        return 1
    
    return 0

if __name__ == "__main__":
    sys.exit(main())