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
    print("FIXING SEARCH HEADER OVERLAP")
    print("=" * 35)
    print("🔧 Problem: Green header with search gets covered by red")
    print("🎯 Solution: Extra bottom padding when search is present")
    print("   • No search: 60px padding (normal)")
    print("   • With search: 80px bottom padding (extra space)")
    
    try:
        ftp = ftplib.FTP(HOST, USER, PASS)
        ftp.cwd(PATH)
        print("✓ Connected to FTP")
        
        # Upload green header with search-specific padding
        with open("/Applications/XAMPP/xamppfiles/htdocs/common-components/page-section-header.php", 'rb') as f:
            ftp.storbinary('STOR common-components/page-section-header.php', f)
        print("✓ Uploaded: page-section-header.php with search padding fix")
        
        ftp.quit()
        
        print("\n🎉 SEARCH PADDING FIX DEPLOYED!")
        print("✅ Homepage (with search): More bottom padding")
        print("✅ Other pages (no search): Normal padding")
        print("✅ Red should no longer cover search area")
        
        print("\n" + "=" * 35)
        print("TEST COMPARISON:")
        print("🏠 https://11klassniki.ru/ (with search)")
        print("📋 https://11klassniki.ru/vpo-all-regions (no search)")
        print("Both should have green fully visible!")
        print("=" * 35)
        
    except Exception as e:
        print(f"✗ Error: {str(e)}")
        return 1
    
    return 0

if __name__ == "__main__":
    sys.exit(main())