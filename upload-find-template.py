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
        
        with open("/Applications/XAMPP/xamppfiles/htdocs/find-login-template.php", 'rb') as f:
            ftp.storbinary('STOR find-login-template.php', f)
        print("✓ Uploaded: find-login-template.php")
        
        ftp.quit()
        
        print("\nDiagnostic: https://11klassniki.ru/find-login-template.php")
        print("This will show which template has the old SVG code")
        
    except Exception as e:
        print(f"✗ Error: {str(e)}")
        return 1
    
    return 0

if __name__ == "__main__":
    sys.exit(main())