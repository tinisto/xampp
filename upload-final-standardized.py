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
    print("🔧 Final Standardized Field Names")
    print("=" * 40)
    print("Uploading version with complete standardization")
    
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
        
        print("=" * 40)
        print("✅ Final standardized version uploaded!")
        print("")
        print("📝 STANDARDIZED FIELD NAMING CONVENTIONS:")
        print("")
        print("🌍 REGIONS:")
        print("   • $region_url_slug = URL parameter from request")
        print("   • $region_database_id = Primary key (id_region)")
        print("   • $region_display_name = Russian display name")
        print("   • $region_url_slug_stored = URL slug from DB")
        print("")
        print("🏙️ TOWNS:")
        print("   • $town_database_id = Primary key (id_town)")
        print("   • $town_display_name = Russian display name")
        print("   • $town_url_slug = URL slug (town_name_en)")
        print("   • $towns_result = Query result object")
        print("")
        print("🏫 INSTITUTIONS:")
        print("   • $institution_type = Type (vpo/spo/schools)")
        print("   • $institutions_result = Query result object")
        print("   • $total_institutions = Total count")
        print("")
        print("📄 PAGINATION:")
        print("   • $current_page = Current page number")
        print("   • $items_per_page = Items per page limit")
        print("   • $total_pages = Total number of pages")
        print("")
        print("⚠️  DATABASE INCONSISTENCIES TO FIX:")
        print("   • Regions table: uses id_region (not region_id)")
        print("   • VPO table: uses region_id, town_id")
        print("   • SPO table: uses id_region, id_town")
        print("   • Schools table: uses id_region, id_town")
        print("   • Towns table: uses id_town, id_region")
        print("   • Areas table: uses id_area, id_region")
        print("")
        print("🔗 Test URLs:")
        print("   • https://11klassniki.ru/vpo-in-region/altayskiy-kray")
        print("   • https://11klassniki.ru/spo-in-region/amurskaya-oblast")
        print("   • https://11klassniki.ru/schools-in-region/altayskiy-kray")
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()