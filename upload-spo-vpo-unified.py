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
    print("🎓 SPO/VPO Single Page Fix")
    print("=" * 30)
    print("Uploading unified SPO/VPO single page")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST, FTP_USER, FTP_PASS)
        ftp.cwd(FTP_PATH)
        print("✓ Connected to FTP server")
        
        # Upload files
        with open('pages/common/vpo-spo/single.php', 'rb') as f:
            ftp.storbinary('STOR pages/common/vpo-spo/single.php', f)
        print("✓ Uploaded: single.php")
        
        with open('pages/common/vpo-spo/single-unified.php', 'rb') as f:
            ftp.storbinary('STOR pages/common/vpo-spo/single-unified.php', f)
        print("✓ Uploaded: single-unified.php")
        
        ftp.quit()
        
        print("=" * 30)
        print("✅ SPO/VPO pages fixed!")
        print("")
        print("📝 Fixed issues:")
        print("   • Fixed template engine call")
        print("   • Added proper header/footer")
        print("   • Added page header with badge")
        print("   • Professional layout with sidebar")
        print("   • Handles both SPO and VPO")
        print("")
        print("🔗 Test URLs:")
        print("   • https://11klassniki.ru/spo/belogorskiy-tehnologicheskiy-tehnikum-pischevoy-promyishlennosti")
        print("   • https://11klassniki.ru/vpo/amurskiy-gosudarstvennyiy-universitet")
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()