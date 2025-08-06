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
    print("🔧 Universal VPO/SPO/Schools Page")
    print("=" * 35)
    print("Uploading version that handles all types")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST, FTP_USER, FTP_PASS)
        ftp.cwd(FTP_PATH)
        print("✓ Connected to FTP server")
        
        # Upload universal version
        with open('pages/common/educational-institutions-in-region/educational-institutions-in-region.php', 'rb') as f:
            ftp.storbinary('STOR pages/common/educational-institutions-in-region/educational-institutions-in-region.php', f)
        print("✓ Uploaded: educational-institutions-in-region.php")
        
        ftp.quit()
        
        print("=" * 35)
        print("✅ Universal version uploaded!")
        print("📝 Correctly handles:")
        print("   • VPO: uses region_id column")
        print("   • SPO: uses id_region column")  
        print("   • Schools: uses id_region column")
        print("   • Different name/address fields")
        print("   • Shows title in green header")
        print("")
        print("🔗 Test all types:")
        print("   • https://11klassniki.ru/vpo-in-region/amurskaya-oblast")
        print("   • https://11klassniki.ru/spo-in-region/amurskaya-oblast")
        print("   • https://11klassniki.ru/schools-in-region/amurskaya-oblast")
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()