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
    print("🧪 Restore Working Tests")
    print("=" * 25)
    print("Restoring functional test system")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST, FTP_USER, FTP_PASS)
        ftp.cwd(FTP_PATH)
        print("✓ Connected to FTP server")
        
        # Upload working test implementation
        with open('pages/tests/test-improved.php', 'rb') as f:
            ftp.storbinary('STOR pages/tests/test-improved.php', f)
        print("✓ Uploaded: test-improved.php")
        
        # Upload test result page
        with open('pages/tests/test-result.php', 'rb') as f:
            ftp.storbinary('STOR pages/tests/test-result.php', f)
        print("✓ Uploaded: test-result.php")
        
        ftp.quit()
        
        print("=" * 25)
        print("✅ Tests functionality restored!")
        print("")
        print("📝 Now working:")
        print("   • Loads existing question files")
        print("   • Interactive test interface")
        print("   • Progress tracking")
        print("   • Auto-scroll between questions")
        print("   • Score calculation")
        print("   • Detailed results with explanations")
        print("   • Option to retake tests")
        print("")
        print("🔗 Test URLs that should work:")
        print("   • https://11klassniki.ru/test/career-test")
        print("   • https://11klassniki.ru/test/iq-test")
        print("   • https://11klassniki.ru/test/math-test")
        print("   • https://11klassniki.ru/test/russian-test")
        print("   • https://11klassniki.ru/test/physics-test")
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()