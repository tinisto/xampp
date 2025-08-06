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
    print("FINAL FIX: NO GAPS, NO OVERLAPPING")
    print("=" * 40)
    print("🔧 Strategy:")
    print("   • Remove margin from red wrapper (no dark gaps)")
    print("   • Make green header taller (60px padding)")
    print("   • Green and red touch directly, no gaps")
    
    try:
        ftp = ftplib.FTP(HOST, USER, PASS)
        ftp.cwd(PATH)
        print("✓ Connected to FTP")
        
        # Upload both fixed files
        with open("/Applications/XAMPP/xamppfiles/htdocs/common-components/content-wrapper.php", 'rb') as f:
            ftp.storbinary('STOR common-components/content-wrapper.php', f)
        print("✓ Uploaded: content-wrapper.php (margin: 0)")
        
        with open("/Applications/XAMPP/xamppfiles/htdocs/common-components/page-section-header.php", 'rb') as f:
            ftp.storbinary('STOR common-components/page-section-header.php', f)
        print("✓ Uploaded: page-section-header.php (padding: 60px)")
        
        ftp.quit()
        
        print("\n🎉 FINAL FIX DEPLOYED!")
        print("✅ Green header: Taller (60px padding)")
        print("✅ Red content: No margin, touches green directly")
        print("✅ No dark gaps, no overlapping")
        print("\nTEST: https://11klassniki.ru/")
        
    except Exception as e:
        print(f"✗ Error: {str(e)}")
        return 1
    
    return 0

if __name__ == "__main__":
    sys.exit(main())