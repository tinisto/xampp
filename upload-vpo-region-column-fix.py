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
    print("🔧 VPO Region Column Name Fix")
    print("=" * 32)
    print("Fixing database column name issue")
    
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
        
        print("=" * 32)
        print("✅ Column name fix applied!")
        print("📝 Changes:")
        print("   • Auto-detect column name (region_id vs id_region)")
        print("   • VPO table uses region_id")
        print("   • SPO/Schools tables use id_region")
        print("   • Updated towns query")
        print("")
        print("🔗 Test: https://11klassniki.ru/vpo-in-region/amurskaya-oblast")
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()