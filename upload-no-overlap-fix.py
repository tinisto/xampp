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
    print("NO OVERLAP FIX - FLEX LAYOUT")
    print("=" * 50)
    print("🔧 Problem: Yellow overlapping green")
    print("🎯 Solution: Flex container with proper stacking")
    print("   • Green header: flex item")
    print("   • Yellow search: flex item below green")
    print("   • Red content: separate below")
    
    try:
        ftp = ftplib.FTP(HOST, USER, PASS)
        ftp.cwd(PATH)
        print("✓ Connected to FTP")
        
        # Upload both fixed files
        with open("/Applications/XAMPP/xamppfiles/htdocs/common-components/template-engine-ultimate.php", 'rb') as f:
            ftp.storbinary('STOR common-components/template-engine-ultimate.php', f)
        print("✓ Uploaded: template-engine-ultimate.php (flex layout)")
        
        with open("/Applications/XAMPP/xamppfiles/htdocs/common-components/page-section-header-simple.php", 'rb') as f:
            ftp.storbinary('STOR common-components/page-section-header-simple.php', f)
        print("✓ Uploaded: page-section-header-simple.php (z-index: 2)")
        
        ftp.quit()
        
        print("\n🎉 NO OVERLAP FIX DEPLOYED!")
        print("✅ Flex container ensures proper stacking")
        print("✅ Green → Yellow → Red (no overlaps)")
        print("✅ Each section is separate")
        
        print("\n" + "=" * 50)
        print("TEST: https://11klassniki.ru/")
        print("Should see:")
        print("1. Green header (full width)")
        print("2. Yellow search bar (full width)")
        print("3. Red content (full width)")
        print("No overlapping!")
        print("=" * 50)
        
    except Exception as e:
        print(f"✗ Error: {str(e)}")
        return 1
    
    return 0

if __name__ == "__main__":
    sys.exit(main())