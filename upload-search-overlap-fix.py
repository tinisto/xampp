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
    print("FIXING SEARCH HEADER OVERLAP - FINAL SOLUTION")
    print("=" * 45)
    print("🔧 Problem: Red content covers green header with search")
    print("🎯 Solution: Conditional padding based on search presence")
    print("   • No search: Normal padding (60px)")
    print("   • With search: Extra bottom padding (100px)")
    print("   • Clear div between sections for proper separation")
    
    try:
        ftp = ftplib.FTP(HOST, USER, PASS)
        ftp.cwd(PATH)
        print("✓ Connected to FTP")
        
        # Upload green header with conditional classes
        with open("/Applications/XAMPP/xamppfiles/htdocs/common-components/page-section-header.php", 'rb') as f:
            ftp.storbinary('STOR common-components/page-section-header.php', f)
        print("✓ Uploaded: page-section-header.php (with-search/no-search classes)")
        
        # Upload template engine with clear div
        with open("/Applications/XAMPP/xamppfiles/htdocs/common-components/template-engine-ultimate.php", 'rb') as f:
            ftp.storbinary('STOR common-components/template-engine-ultimate.php', f)
        print("✓ Uploaded: template-engine-ultimate.php (clear div added)")
        
        ftp.quit()
        
        print("\n🎉 SEARCH OVERLAP FIX DEPLOYED!")
        print("✅ Pages with search: Extra bottom padding (100px)")
        print("✅ Pages without search: Normal padding (60px)")
        print("✅ Clear div ensures proper separation")
        
        print("\n" + "=" * 45)
        print("TEST THESE PAGES:")
        print("🔍 WITH SEARCH: https://11klassniki.ru/")
        print("📋 NO SEARCH: https://11klassniki.ru/vpo-all-regions")
        print("📄 NO SEARCH: https://11klassniki.ru/about")
        print("\nAll should show green header fully visible!")
        print("=" * 45)
        
    except Exception as e:
        print(f"✗ Error: {str(e)}")
        return 1
    
    return 0

if __name__ == "__main__":
    sys.exit(main())