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
    print("🔧 Simple VPO Region Page")
    print("=" * 28)
    print("Uploading simple self-contained version")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST, FTP_USER, FTP_PASS)
        ftp.cwd(FTP_PATH)
        print("✓ Connected to FTP server")
        
        # Upload the simple version
        with open('pages/common/educational-institutions-in-region/educational-institutions-in-region.php', 'rb') as f:
            ftp.storbinary('STOR pages/common/educational-institutions-in-region/educational-institutions-in-region.php', f)
        print("✓ Uploaded: educational-institutions-in-region.php")
        
        ftp.quit()
        
        print("=" * 28)
        print("✅ Simple version uploaded!")
        print("📝 Features:")
        print("   • Auto-detects column names")
        print("   • Shows VPO title in green header")
        print("   • No dependencies on other files")
        print("   • Handles different table structures")
        print("")
        print("🔗 Test: https://11klassniki.ru/vpo-in-region/amurskaya-oblast")
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()