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
    print("🔧 VPO Debug Test Version")
    print("=" * 26)
    print("Uploading minimal debug version")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST, FTP_USER, FTP_PASS)
        ftp.cwd(FTP_PATH)
        print("✓ Connected to FTP server")
        
        # Upload test version
        with open('pages/common/educational-institutions-in-region/educational-institutions-in-region.php', 'rb') as f:
            ftp.storbinary('STOR pages/common/educational-institutions-in-region/educational-institutions-in-region.php', f)
        print("✓ Uploaded: educational-institutions-in-region.php (debug)")
        
        ftp.quit()
        
        print("=" * 26)
        print("✅ Debug version uploaded!")
        print("📝 This will show:")
        print("   • Database connection status")
        print("   • Region lookup results")
        print("   • Table column structure")
        print("   • Any error messages")
        print("")
        print("🔗 Test: https://11klassniki.ru/vpo-in-region/amurskaya-oblast")
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()