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
    print("YELLOW SEARCH DIV UPLOAD")
    print("=" * 50)
    print("🎨 Changes:")
    print("   • Search div background: YELLOW (for testing)")
    print("   • Padding: 20px top/bottom")
    print("   • Main page has showSearch: true")
    
    try:
        ftp = ftplib.FTP(HOST, USER, PASS)
        ftp.cwd(PATH)
        print("✓ Connected to FTP")
        
        # Upload the updated template engine with yellow search div
        with open("/Applications/XAMPP/xamppfiles/htdocs/common-components/template-engine-ultimate.php", 'rb') as f:
            ftp.storbinary('STOR common-components/template-engine-ultimate.php', f)
        print("✓ Uploaded: template-engine-ultimate.php (yellow search div)")
        
        # Make sure other components are uploaded
        with open("/Applications/XAMPP/xamppfiles/htdocs/common-components/page-section-header-simple.php", 'rb') as f:
            ftp.storbinary('STOR common-components/page-section-header-simple.php', f)
        print("✓ Uploaded: page-section-header-simple.php")
        
        with open("/Applications/XAMPP/xamppfiles/htdocs/common-components/search-bar.php", 'rb') as f:
            ftp.storbinary('STOR common-components/search-bar.php', f)
        print("✓ Uploaded: search-bar.php")
        
        ftp.quit()
        
        print("\n🎉 YELLOW SEARCH DEPLOYED!")
        print("✅ Main page should show:")
        print("   1. Header (white)")
        print("   2. Green title section")
        print("   3. YELLOW search section")
        print("   4. Red content")
        
        print("\n" + "=" * 50)
        print("TEST: https://11klassniki.ru/")
        print("Look for YELLOW section with search bar!")
        print("=" * 50)
        
    except Exception as e:
        print(f"✗ Error: {str(e)}")
        return 1
    
    return 0

if __name__ == "__main__":
    sys.exit(main())