#!/usr/bin/env python3

import ftplib
import os
import sys

# FTP Configuration
FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
FTP_PATH = '/11klassnikiru/'

def main():
    print("🧪 Tests Unified Cards")
    print("=" * 25)
    print("Updating tests to use reusable card components")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST, FTP_USER, FTP_PASS)
        ftp.cwd(FTP_PATH)
        print("✓ Connected to FTP server")
        
        # Upload test card component
        with open('common-components/test-card.php', 'rb') as f:
            ftp.storbinary('STOR common-components/test-card.php', f)
        print("✓ Uploaded: test-card.php")
        
        # Upload updated tests main page
        with open('pages/tests/tests-main.php', 'rb') as f:
            ftp.storbinary('STOR pages/tests/tests-main.php', f)
        print("✓ Uploaded: tests-main.php")
        
        ftp.quit()
        
        print("=" * 25)
        print("✅ Tests page unified!")
        print("")
        print("📝 Now using same card design as:")
        print("   • News cards")
        print("   • Post cards")
        print("   • Same hover effects")
        print("   • Same responsive grid")
        print("   • Consistent styling")
        print("")
        print("🔗 Test: https://11klassniki.ru/tests")
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()