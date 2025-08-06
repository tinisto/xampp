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
    print("SITE ICON & FOOTER UPDATES")
    print("=" * 50)
    print("🔧 Changes:")
    print("   • Added SVG site icon variant (like login page)")
    print("   • Updated login/registration forms to use reusable component")
    print("   • Footer: Copyright moved to center between icon and nav")
    print("   • Footer: One line copyright text")
    print("   • Footer: Clean 3-column layout")
    
    try:
        ftp = ftplib.FTP(HOST, USER, PASS)
        ftp.cwd(PATH)
        print("✓ Connected to FTP")
        
        # Upload updated site icon component
        with open("/Applications/XAMPP/xamppfiles/htdocs/common-components/site-icon.php", 'rb') as f:
            ftp.storbinary('STOR common-components/site-icon.php', f)
        print("✓ Uploaded: site-icon.php (added SVG variant)")
        
        # Upload updated form template
        with open("/Applications/XAMPP/xamppfiles/htdocs/includes/form-template-fixed.php", 'rb') as f:
            ftp.storbinary('STOR includes/form-template-fixed.php', f)
        print("✓ Uploaded: form-template-fixed.php (uses reusable icon)")
        
        # Upload updated footer
        with open("/Applications/XAMPP/xamppfiles/htdocs/common-components/footer-unified.php", 'rb') as f:
            ftp.storbinary('STOR common-components/footer-unified.php', f)
        print("✓ Uploaded: footer-unified.php (centered copyright)")
        
        ftp.quit()
        
        print("\n🎉 SITE ICON & FOOTER UPDATED!")
        print("✅ Consistent site icons across all pages")
        print("✅ Login/register use reusable component")
        print("✅ Footer: Icon | Copyright | Navigation")
        print("✅ Clean one-line copyright")
        
        print("\n" + "=" * 50)
        print("TEST THESE PAGES:")
        print("🔍 https://11klassniki.ru/ (footer layout)")
        print("🔐 https://11klassniki.ru/login (consistent icon)")
        print("📝 https://11klassniki.ru/registration (consistent icon)")
        print("=" * 50)
        
    except Exception as e:
        print(f"✗ Error: {str(e)}")
        return 1
    
    return 0

if __name__ == "__main__":
    sys.exit(main())