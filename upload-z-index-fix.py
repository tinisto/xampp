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
    print("FIXING Z-INDEX TO STOP RED COVERING GREEN")
    print("=" * 45)
    print("🔧 Adding z-index to ensure proper layering:")
    print("   Green header: z-index 10 (on top)")
    print("   Red content: z-index 1 (below)")
    
    try:
        ftp = ftplib.FTP(HOST, USER, PASS)
        ftp.cwd(PATH)
        print("✓ Connected to FTP")
        
        # Upload green header with higher z-index
        with open("/Applications/XAMPP/xamppfiles/htdocs/common-components/page-section-header.php", 'rb') as f:
            ftp.storbinary('STOR common-components/page-section-header.php', f)
        print("✓ Uploaded: page-section-header.php (z-index: 10)")
        
        # Upload content wrapper with lower z-index  
        with open("/Applications/XAMPP/xamppfiles/htdocs/common-components/content-wrapper.php", 'rb') as f:
            ftp.storbinary('STOR common-components/content-wrapper.php', f)
        print("✓ Uploaded: content-wrapper.php (z-index: 1)")
        
        ftp.quit()
        
        print("\n🎉 Z-INDEX FIX DEPLOYED!")
        print("✅ Green header should now stay ABOVE red content")
        print("✅ No more red covering green")
        
        print("\n" + "=" * 45)
        print("TEST: https://11klassniki.ru/")
        print("Green header should be fully visible above red!")
        print("=" * 45)
        
    except Exception as e:
        print(f"✗ Error: {str(e)}")
        return 1
    
    return 0

if __name__ == "__main__":
    sys.exit(main())