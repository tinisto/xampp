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
    print("HEADER CONSISTENCY FIXES")
    print("=" * 50)
    print("🔧 Fixes:")
    print("   • Removed 'Главная' link from navigation")
    print("   • Header site icon: changed to 'small' (matches footer)")
    print("   • Header nav links: 14px font size (matches footer)")
    print("   • Header nav links: system fonts (matches footer)")
    print("   • Footer: removed 'Образовательный портал России'")
    print("   • Footer: removed 'support@11klassniki.ru'")
    
    try:
        ftp = ftplib.FTP(HOST, USER, PASS)
        ftp.cwd(PATH)
        print("✓ Connected to FTP")
        
        # Upload updated header
        with open("/Applications/XAMPP/xamppfiles/htdocs/common-components/header.php", 'rb') as f:
            ftp.storbinary('STOR common-components/header.php', f)
        print("✓ Uploaded: header.php")
        
        # Upload updated footer
        with open("/Applications/XAMPP/xamppfiles/htdocs/common-components/footer-unified.php", 'rb') as f:
            ftp.storbinary('STOR common-components/footer-unified.php', f)
        print("✓ Uploaded: footer-unified.php")
        
        ftp.quit()
        
        print("\n🎉 HEADER & FOOTER CONSISTENCY FIXED!")
        print("✅ No more 'Главная' link")
        print("✅ Site icon: same size as footer")
        print("✅ Nav links: same font size as footer")
        print("✅ Typography: consistent across header/footer")
        print("✅ Footer: cleaner, just icon and links")
        print("✅ Footer: removed extra text")
        
        print("\n" + "=" * 50)
        print("TEST:")
        print("🔍 https://11klassniki.ru/")
        print("   • Header and footer icons same size")
        print("   • Header and footer links same font size")
        print("   • No 'Главная' link in navigation")
        print("=" * 50)
        
    except Exception as e:
        print(f"✗ Error: {str(e)}")
        return 1
    
    return 0

if __name__ == "__main__":
    sys.exit(main())