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
    print("FORCING SEPARATION WITH TOP MARGIN")
    print("=" * 40)
    print("🔧 Adding 50px top margin to red content wrapper")
    print("🎯 This will physically push red content away from green")
    
    try:
        ftp = ftplib.FTP(HOST, USER, PASS)
        ftp.cwd(PATH)
        print("✓ Connected to FTP")
        
        # Upload content wrapper with top margin
        with open("/Applications/XAMPP/xamppfiles/htdocs/common-components/content-wrapper.php", 'rb') as f:
            ftp.storbinary('STOR common-components/content-wrapper.php', f)
        print("✓ Uploaded: content-wrapper.php with 50px top margin")
        
        ftp.quit()
        
        print("\n✅ MARGIN FIX DEPLOYED!")
        print("Red content should now be pushed 50px below green header")
        print("\nTEST: https://11klassniki.ru/")
        print("There should be clear separation between green and red!")
        
    except Exception as e:
        print(f"✗ Error: {str(e)}")
        return 1
    
    return 0

if __name__ == "__main__":
    sys.exit(main())