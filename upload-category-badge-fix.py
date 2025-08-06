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
    print("🔧 Category Page Badge Implementation")
    print("=" * 40)
    print("Moving article count from white section to header badge")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST, FTP_USER, FTP_PASS)
        ftp.cwd(FTP_PATH)
        print("✓ Connected to FTP server")
        
        # Files to upload
        files = [
            ('pages/category/category.php', 'pages/category/category.php'),
            ('pages/category/category-content-unified.php', 'pages/category/category-content-unified.php'),
            ('common-components/page-section-header.php', 'common-components/page-section-header.php')
        ]
        
        for local_file, remote_file in files:
            with open(local_file, 'rb') as f:
                ftp.storbinary(f'STOR {remote_file}', f)
            print(f"✓ Uploaded: {local_file}")
        
        ftp.quit()
        
        print("=" * 40)
        print("✅ Category badge implementation complete!")
        print("📝 Changes:")
        print("   • Removed white section with article count")
        print("   • Added article count as badge in header")
        print("   • Badge shows: '88 статей'")
        print("")
        print("🔗 Test: https://11klassniki.ru/category/abiturientam")
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()