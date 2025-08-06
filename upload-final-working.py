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
    print("🚀 Final Working Version")
    print("=" * 35)
    print("Uploading version with correct column names")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST, FTP_USER, FTP_PASS)
        ftp.cwd(FTP_PATH)
        print("✓ Connected to FTP server")
        
        # Upload working version
        with open('pages/common/educational-institutions-in-region/educational-institutions-in-region.php', 'rb') as f:
            ftp.storbinary('STOR pages/common/educational-institutions-in-region/educational-institutions-in-region.php', f)
        print("✓ Uploaded: educational-institutions-in-region.php")
        
        ftp.quit()
        
        print("=" * 35)
        print("✅ Final working version uploaded!")
        print("")
        print("📝 CORRECTED COLUMN NAMES:")
        print("   • regions.region_id (not id_region)")
        print("   • towns.town_id (not id_town)")
        print("   • vpo/spo/schools.id (not id_vpo, etc)")
        print("   • All tables use region_id")
        print("   • All tables use town_id")
        print("")
        print("🔗 Test URLs:")
        print("   • https://11klassniki.ru/vpo-in-region/amurskaya-oblast")
        print("   • https://11klassniki.ru/spo-in-region/amurskaya-oblast")
        print("   • https://11klassniki.ru/schools-in-region/amurskaya-oblast")
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()