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
    print("🔧 VPO Region Page Header Addition")
    print("=" * 36)
    print("Adding green header to existing working page")
    
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
        
        print("=" * 36)
        print("✅ Green header added!")
        print("📝 Changes:")
        print("   • Added page-section-header component")
        print("   • Title now appears in green header")
        print("   • Removed duplicate H1 title")
        print("   • Rest of page remains unchanged")
        print("")
        print("🔗 Test: https://11klassniki.ru/vpo-in-region/altayskiy-kray")
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()