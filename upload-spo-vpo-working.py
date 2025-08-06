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
    print("🎓 SPO/VPO Working Version")
    print("=" * 30)
    print("Uploading working SPO/VPO pages")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST, FTP_USER, FTP_PASS)
        ftp.cwd(FTP_PATH)
        print("✓ Connected to FTP server")
        
        # Upload files
        with open('pages/common/vpo-spo/single.php', 'rb') as f:
            ftp.storbinary('STOR pages/common/vpo-spo/single.php', f)
        print("✓ Uploaded: single.php")
        
        with open('pages/common/vpo-spo/single-working.php', 'rb') as f:
            ftp.storbinary('STOR pages/common/vpo-spo/single-working.php', f)
        print("✓ Uploaded: single-working.php")
        
        ftp.quit()
        
        print("=" * 30)
        print("✅ Working version active!")
        print("")
        print("📝 Using direct HTML output")
        print("   • Proper header/footer")
        print("   • Page header with badge")
        print("   • Professional layout")
        print("   • Handles both SPO and VPO")
        print("")
        print("🔗 Test URLs:")
        print("   • https://11klassniki.ru/spo/belogorskiy-tehnologicheskiy-tehnikum-pischevoy-promyishlennosti")
        print("   • https://11klassniki.ru/vpo/amijt")
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()