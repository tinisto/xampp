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
    print("🔧 Fix VPO Region Page Error")
    print("=" * 30)
    print("Fixing Internal Server Error")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST, FTP_USER, FTP_PASS)
        ftp.cwd(FTP_PATH)
        print("✓ Connected to FTP server")
        
        # Upload the fixed file
        with open('pages/common/educational-institutions-in-region/educational-institutions-in-region.php', 'rb') as f:
            ftp.storbinary('STOR pages/common/educational-institutions-in-region/educational-institutions-in-region.php', f)
        print("✓ Uploaded: educational-institutions-in-region.php")
        
        ftp.quit()
        
        print("=" * 30)
        print("✅ Error fixed!")
        print("📝 Fixed:")
        print("   • Changed templateConfig to additionalData")
        print("   • Added region_name_en to data")
        print("")
        print("🔗 Test: https://11klassniki.ru/vpo-in-region/altayskiy-kray")
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()