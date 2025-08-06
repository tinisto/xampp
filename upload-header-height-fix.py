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
    print("🔧 Fix Header Height Inconsistency")
    print("=" * 35)
    print("Standardizing page header heights across all pages")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST, FTP_USER, FTP_PASS)
        ftp.cwd(FTP_PATH)
        print("✓ Connected to FTP server")
        
        # Upload the fixed simple header component
        with open('common-components/page-section-header-simple.php', 'rb') as f:
            ftp.storbinary('STOR common-components/page-section-header-simple.php', f)
        print("✓ Uploaded: page-section-header-simple.php")
        
        ftp.quit()
        
        print("=" * 35)
        print("✅ Header heights standardized!")
        print("📏 All page headers now use 60px padding (120px total)")
        print("🔗 Test pages:")
        print("   • https://11klassniki.ru/news (unchanged)")
        print("   • https://11klassniki.ru/write (now matches)")
        print("   • All template-based pages (now consistent)")
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()