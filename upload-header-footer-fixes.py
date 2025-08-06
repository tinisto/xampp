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
    print("HEADER & FOOTER FIXES")
    print("=" * 50)
    print("🔧 Fixes:")
    print("   • Created reusable site icon component")
    print("   • Header: Replace text with site icon (always visible)")
    print("   • Header: Fix avatar with !important styles")
    print("   • Footer: Add site icon, match header fonts")
    
    try:
        ftp = ftplib.FTP(HOST, USER, PASS)
        ftp.cwd(PATH)
        print("✓ Connected to FTP")
        
        # Upload new site icon component
        with open("/Applications/XAMPP/xamppfiles/htdocs/common-components/site-icon.php", 'rb') as f:
            ftp.storbinary('STOR common-components/site-icon.php', f)
        print("✓ Uploaded: site-icon.php (reusable component)")
        
        # Upload updated header
        with open("/Applications/XAMPP/xamppfiles/htdocs/common-components/header.php", 'rb') as f:
            ftp.storbinary('STOR common-components/header.php', f)
        print("✓ Uploaded: header.php (site icon + avatar fixes)")
        
        # Upload updated footer
        with open("/Applications/XAMPP/xamppfiles/htdocs/common-components/footer-unified.php", 'rb') as f:
            ftp.storbinary('STOR common-components/footer-unified.php', f)
        print("✓ Uploaded: footer-unified.php (icon + font matching)")
        
        ftp.quit()
        
        print("\n🎉 HEADER & FOOTER UPDATED!")
        print("✅ Site icon: Always visible green gradient")
        print("✅ Header: No more invisible text in white mode")
        print("✅ Avatar: Green background, white icon")
        print("✅ Footer: Matches header fonts, includes icon")
        
        print("\n" + "=" * 50)
        print("TEST:")
        print("🔍 https://11klassniki.ru/")
        print("   • Toggle light/dark mode")
        print("   • Check header visibility")
        print("   • Check avatar appearance")
        print("   • Check footer consistency")
        print("=" * 50)
        
    except Exception as e:
        print(f"✗ Error: {str(e)}")
        return 1
    
    return 0

if __name__ == "__main__":
    sys.exit(main())