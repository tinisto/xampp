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
    print("🔧 Debug Header Spacing")
    print("=" * 30)
    print("Adding debug borders to identify spacing issues")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST, FTP_USER, FTP_PASS)
        ftp.cwd(FTP_PATH)
        print("✓ Connected to FTP server")
        
        # Upload debug file
        local_file = 'common-components/page-section-header.php'
        remote_file = 'common-components/page-section-header.php'
        
        with open(local_file, 'rb') as f:
            ftp.storbinary(f'STOR {remote_file}', f)
        
        print(f"✓ Uploaded: {local_file}")
        ftp.quit()
        
        print("=" * 30)
        print("✅ Debug borders added!")
        print("🔗 Test pages:")
        print("   • https://11klassniki.ru/write")
        print("   • https://11klassniki.ru/about")
        print("📝 Look for:")
        print("   • RED borders = outer green section")
        print("   • BLUE borders = inner container")
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()