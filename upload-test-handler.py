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
    print("🧪 Test Handler Fix")
    print("=" * 20)
    print("Creating missing test handler")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST, FTP_USER, FTP_PASS)
        ftp.cwd(FTP_PATH)
        print("✓ Connected to FTP server")
        
        # Upload test handler
        with open('pages/tests/test-improved.php', 'rb') as f:
            ftp.storbinary('STOR pages/tests/test-improved.php', f)
        print("✓ Uploaded: test-improved.php")
        
        ftp.quit()
        
        print("=" * 20)
        print("✅ Test handler created!")
        print("")
        print("📝 What it does:")
        print("   • Shows proper message for unavailable tests")
        print("   • Prevents empty colored divs")
        print("   • Provides link back to test list")
        print("   • Uses unified template")
        print("")
        print("🔗 Test: https://11klassniki.ru/test/physics-test")
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()