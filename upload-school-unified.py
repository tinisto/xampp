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
    print("🏫 School Page Unified Template")
    print("=" * 35)
    print("Uploading school page with unified design")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST, FTP_USER, FTP_PASS)
        ftp.cwd(FTP_PATH)
        print("✓ Connected to FTP server")
        
        # Upload school page
        with open('pages/school/school-single.php', 'rb') as f:
            ftp.storbinary('STOR pages/school/school-single.php', f)
        print("✓ Uploaded: school-single.php")
        
        # Also upload the simplified version redirect
        with open('pages/school/school-single-simplified.php', 'rb') as f:
            ftp.storbinary('STOR pages/school/school-single-simplified.php', f)
        print("✓ Uploaded: school-single-simplified.php")
        
        ftp.quit()
        
        print("=" * 35)
        print("✅ School page updated!")
        print("")
        print("📝 Fixed issues:")
        print("   • Added proper header component")
        print("   • Added page header with green background")
        print("   • Added location badge")
        print("   • Using unified footer")
        print("   • Professional layout with sidebar")
        print("")
        print("🔗 Test: https://11klassniki.ru/school/ilezskaya-ileza")
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()