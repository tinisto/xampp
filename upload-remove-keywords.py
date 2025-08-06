#!/usr/bin/env python3

import ftplib
import os
import sys

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def main():
    print("REMOVE META KEYWORDS UPDATE")
    print("=" * 50)
    print("🔧 Changes:")
    print("   • Removed meta keywords from template engine")
    print("   • Removed metaK from category pages")
    print("   • Database standardization script ready")
    
    try:
        ftp = ftplib.FTP(HOST, USER, PASS)
        ftp.cwd(PATH)
        print("✓ Connected to FTP")
        
        # Upload updated files
        with open("/Applications/XAMPP/xamppfiles/htdocs/common-components/template-engine-ultimate.php", 'rb') as f:
            ftp.storbinary('STOR common-components/template-engine-ultimate.php', f)
        print("✓ Uploaded: template-engine-ultimate.php")
        
        with open("/Applications/XAMPP/xamppfiles/htdocs/pages/category/category-data-fetch.php", 'rb') as f:
            ftp.storbinary('STOR pages/category/category-data-fetch.php', f)
        print("✓ Uploaded: category-data-fetch.php")
        
        with open("/Applications/XAMPP/xamppfiles/htdocs/pages/category/category.php", 'rb') as f:
            ftp.storbinary('STOR pages/category/category.php', f)
        print("✓ Uploaded: category.php")
        
        # Upload database scripts
        with open("/Applications/XAMPP/xamppfiles/htdocs/check-meta-fields.php", 'rb') as f:
            ftp.storbinary('STOR check-meta-fields.php', f)
        print("✓ Uploaded: check-meta-fields.php")
        
        with open("/Applications/XAMPP/xamppfiles/htdocs/standardize-database.php", 'rb') as f:
            ftp.storbinary('STOR standardize-database.php', f)
        print("✓ Uploaded: standardize-database.php")
        
        with open("/Applications/XAMPP/xamppfiles/htdocs/standardize-database.sql", 'rb') as f:
            ftp.storbinary('STOR standardize-database.sql', f)
        print("✓ Uploaded: standardize-database.sql")
        
        ftp.quit()
        
        print("\n🎉 META KEYWORDS REMOVED!")
        print("✅ Template no longer outputs meta keywords")
        print("✅ Category pages updated")
        
        print("\n" + "=" * 50)
        print("DATABASE STANDARDIZATION:")
        print("\n1. Check current meta fields:")
        print("   https://11klassniki.ru/check-meta-fields.php")
        print("\n2. Run standardization (BACKUP DATABASE FIRST!):")
        print("   https://11klassniki.ru/standardize-database.php")
        print("\n3. SQL script also available:")
        print("   https://11klassniki.ru/standardize-database.sql")
        print("=" * 50)
        
    except Exception as e:
        print(f"✗ Error: {str(e)}")
        return 1
    
    return 0

if __name__ == "__main__":
    sys.exit(main())