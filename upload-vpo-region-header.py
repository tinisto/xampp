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
    print("🔧 VPO Region Page Header Fix")
    print("=" * 35)
    print("Moving title to green page header")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST, FTP_USER, FTP_PASS)
        ftp.cwd(FTP_PATH)
        print("✓ Connected to FTP server")
        
        # Upload the updated file
        with open('pages/common/educational-institutions-in-region/educational-institutions-in-region.php', 'rb') as f:
            ftp.storbinary('STOR pages/common/educational-institutions-in-region/educational-institutions-in-region.php', f)
        print("✓ Uploaded: educational-institutions-in-region.php")
        
        ftp.quit()
        
        print("=" * 35)
        print("✅ VPO region page fixed!")
        print("📝 Changes:")
        print("   • Converted to use template engine")
        print("   • Title now appears in green header")
        print("   • Consistent with other pages")
        print("")
        print("🔗 Test pages:")
        print("   • https://11klassniki.ru/vpo-in-region/altayskiy-kray")
        print("   • https://11klassniki.ru/spo-in-region/altayskiy-kray")
        print("   • https://11klassniki.ru/schools-in-region/altayskiy-kray")
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()