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
    print("SEARCH COMPONENT SOLUTION")
    print("=" * 50)
    print("🔧 New Approach: Separate search from green header")
    print("   • Green header: Simple title only (40px padding)")
    print("   • Search bar: Separate component below green")
    print("   • Search has gradient transition to red content")
    print("   • No more overlap issues!")
    
    try:
        ftp = ftplib.FTP(HOST, USER, PASS)
        ftp.cwd(PATH)
        print("✓ Connected to FTP")
        
        # Create common-components directory if needed
        try:
            ftp.cwd('common-components')
            ftp.cwd('..')
        except:
            ftp.mkd('common-components')
            print("✓ Created common-components directory")
        
        # Upload new components
        with open("/Applications/XAMPP/xamppfiles/htdocs/common-components/page-section-header-simple.php", 'rb') as f:
            ftp.storbinary('STOR common-components/page-section-header-simple.php', f)
        print("✓ Uploaded: page-section-header-simple.php (title only)")
        
        with open("/Applications/XAMPP/xamppfiles/htdocs/common-components/search-bar.php", 'rb') as f:
            ftp.storbinary('STOR common-components/search-bar.php', f)
        print("✓ Uploaded: search-bar.php (reusable search component)")
        
        with open("/Applications/XAMPP/xamppfiles/htdocs/common-components/template-engine-ultimate.php", 'rb') as f:
            ftp.storbinary('STOR common-components/template-engine-ultimate.php', f)
        print("✓ Uploaded: template-engine-ultimate.php (uses new components)")
        
        ftp.quit()
        
        print("\n🎉 SEARCH COMPONENT SOLUTION DEPLOYED!")
        print("✅ Green header: Clean, simple, no search")
        print("✅ Search bar: Separate component with gradient")
        print("✅ No more overlap between green and red!")
        print("✅ Better mobile experience")
        
        print("\n" + "=" * 50)
        print("TEST THESE PAGES:")
        print("🔍 WITH SEARCH: https://11klassniki.ru/")
        print("   Should show: Green header → Search bar → Red content")
        print("📋 NO SEARCH: https://11klassniki.ru/vpo-all-regions")
        print("   Should show: Green header → Red content")
        print("=" * 50)
        
    except Exception as e:
        print(f"✗ Error: {str(e)}")
        return 1
    
    return 0

if __name__ == "__main__":
    sys.exit(main())