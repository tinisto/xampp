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
    print("🔧 Complete Write Page Template Conversion")
    print("=" * 40)
    print("Converting write page to use unified template engine")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST, FTP_USER, FTP_PASS)
        ftp.cwd(FTP_PATH)
        print("✓ Connected to FTP server")
        
        # Files to upload
        files = [
            ('pages/write/write.php', 'pages/write/write.php'),
            ('pages/write/write-content.php', 'pages/write/write-content.php'),
            ('common-components/page-section-header.php', 'common-components/page-section-header.php')
        ]
        
        for local_file, remote_file in files:
            with open(local_file, 'rb') as f:
                ftp.storbinary(f'STOR {remote_file}', f)
            print(f"✓ Uploaded: {local_file}")
        
        ftp.quit()
        
        print("=" * 40)
        print("✅ Template conversion complete!")
        print("🔗 Test page: https://11klassniki.ru/write")
        print("📝 Changes made:")
        print("   • Write page now uses template engine")
        print("   • Consistent layout with other pages")
        print("   • Removed debug red borders")
        print("   • Fixed green header spacing issues")
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()