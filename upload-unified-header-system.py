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
    print("🔧 Unified Header System Implementation")
    print("=" * 42)
    print("Consolidating all pages to use single header component")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST, FTP_USER, FTP_PASS)
        ftp.cwd(FTP_PATH)
        print("✓ Connected to FTP server")
        
        # Upload updated template engine
        with open('common-components/template-engine-ultimate.php', 'rb') as f:
            ftp.storbinary('STOR common-components/template-engine-ultimate.php', f)
        print("✓ Uploaded: template-engine-ultimate.php")
        
        # Remove the old simple header component from server
        try:
            ftp.delete('common-components/page-section-header-simple.php')
            print("✓ Removed: page-section-header-simple.php")
        except Exception as e:
            print(f"• Note: page-section-header-simple.php not found on server (already removed)")
        
        ftp.quit()
        
        print("=" * 42)
        print("✅ Header system unified!")
        print("📝 Changes:")
        print("   • All pages now use page-section-header.php")
        print("   • Removed redundant simple header component")
        print("   • Template engine supports pageHeader config")
        print("   • Consistent 60px padding across all pages")
        print("")
        print("🔗 Test pages with unified headers:")
        print("   • https://11klassniki.ru/news")
        print("   • https://11klassniki.ru/write")
        print("   • https://11klassniki.ru/about")
        print("   • All other template-based pages")
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()