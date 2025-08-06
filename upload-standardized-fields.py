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
    print("🔧 Standardized Field Names")
    print("=" * 35)
    print("Uploading version with clear variable names")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST, FTP_USER, FTP_PASS)
        ftp.cwd(FTP_PATH)
        print("✓ Connected to FTP server")
        
        # Upload standardized version
        with open('pages/common/educational-institutions-in-region/educational-institutions-in-region.php', 'rb') as f:
            ftp.storbinary('STOR pages/common/educational-institutions-in-region/educational-institutions-in-region.php', f)
        print("✓ Uploaded: educational-institutions-in-region.php")
        
        ftp.quit()
        
        print("=" * 35)
        print("✅ Standardized version uploaded!")
        print("📝 Field naming conventions:")
        print("   • $region_url_slug = URL parameter")
        print("   • $region_database_id = DB primary key")
        print("   • $region_display_name = Russian name")
        print("   • $region_url_slug_stored = URL from DB")
        print("   • $institution_type = Type of institution")
        print("   • $current_page = Current page number")
        print("   • $items_per_page = Items per page")
        print("   • $total_institutions = Total count")
        print("   • $institutions_result = Query result")
        print("")
        print("🔗 Test: https://11klassniki.ru/vpo-in-region/altayskiy-kray")
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()