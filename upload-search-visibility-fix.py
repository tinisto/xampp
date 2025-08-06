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
    print("SEARCH VISIBILITY FIX")
    print("=" * 50)
    print("🔧 Problem: Yellow search div not showing")
    print("🎯 Solution: Fixed variable references")
    print("   • Changed $templateConfig to extracted variables")
    print("   • Added debug option (?debug=1)")
    
    try:
        ftp = ftplib.FTP(HOST, USER, PASS)
        ftp.cwd(PATH)
        print("✓ Connected to FTP")
        
        # Upload the fixed template engine
        with open("/Applications/XAMPP/xamppfiles/htdocs/common-components/template-engine-ultimate.php", 'rb') as f:
            ftp.storbinary('STOR common-components/template-engine-ultimate.php', f)
        print("✓ Uploaded: template-engine-ultimate.php (fixed variables)")
        
        ftp.quit()
        
        print("\n🎉 SEARCH VISIBILITY FIX DEPLOYED!")
        print("✅ Variables properly extracted from config")
        print("✅ Yellow search section should now appear")
        
        print("\n" + "=" * 50)
        print("TEST:")
        print("🔍 https://11klassniki.ru/")
        print("   Should show: Green → YELLOW → Red")
        print("\nDEBUG MODE:")
        print("🐛 https://11klassniki.ru/?debug=1")
        print("   Shows orange debug bar with showSearch value")
        print("=" * 50)
        
    except Exception as e:
        print(f"✗ Error: {str(e)}")
        return 1
    
    return 0

if __name__ == "__main__":
    sys.exit(main())