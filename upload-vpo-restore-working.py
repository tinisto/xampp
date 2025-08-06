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
    print("🔧 Restore Working VPO Region Page")
    print("=" * 35)
    print("Uploading known working version")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST, FTP_USER, FTP_PASS)
        ftp.cwd(FTP_PATH)
        print("✓ Connected to FTP server")
        
        # Upload the restore file
        with open('pages/common/educational-institutions-in-region/educational-institutions-in-region-restore.php', 'rb') as f:
            ftp.storbinary('STOR pages/common/educational-institutions-in-region/educational-institutions-in-region.php', f)
        print("✓ Uploaded: educational-institutions-in-region.php (restored)")
        
        ftp.quit()
        
        print("=" * 35)
        print("✅ Working version restored!")
        print("📝 Notes:")
        print("   • Restored to original working state")
        print("   • Title appears as H1 in content")
        print("   • No green header for now")
        print("")
        print("🔗 Test: https://11klassniki.ru/vpo-in-region/altayskiy-kray")
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()