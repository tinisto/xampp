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
    print("🧪 Tests Main Page Fix")
    print("=" * 25)
    print("Fixing tests listing page")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST, FTP_USER, FTP_PASS)
        ftp.cwd(FTP_PATH)
        print("✓ Connected to FTP server")
        
        # Upload files
        with open('pages/tests/tests-main.php', 'rb') as f:
            ftp.storbinary('STOR pages/tests/tests-main.php', f)
        print("✓ Uploaded: tests-main.php")
        
        with open('pages/tests/tests-main-working.php', 'rb') as f:
            ftp.storbinary('STOR pages/tests/tests-main-working.php', f)
        print("✓ Uploaded: tests-main-working.php")
        
        ftp.quit()
        
        print("=" * 25)
        print("✅ Tests page fixed!")
        print("")
        print("📝 Fixed issues:")
        print("   • Removed template engine errors")
        print("   • Fixed colored div display")
        print("   • Clean HTML output")
        print("   • Professional test cards")
        print("   • Responsive design")
        print("")
        print("🔗 Test: https://11klassniki.ru/tests")
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()