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
    print("DATABASE STANDARDIZATION UPLOAD")
    print("=" * 50)
    print("🔧 Uploading corrected database standardization scripts")
    print("   • Correct field names for each table")
    print("   • PHP file updater script")
    
    try:
        ftp = ftplib.FTP(HOST, USER, PASS)
        ftp.cwd(PATH)
        print("✓ Connected to FTP")
        
        # Upload corrected standardization script
        with open("/Applications/XAMPP/xamppfiles/htdocs/standardize-database-correct.php", 'rb') as f:
            ftp.storbinary('STOR standardize-database-correct.php', f)
        print("✓ Uploaded: standardize-database-correct.php")
        
        # Upload PHP file updater
        with open("/Applications/XAMPP/xamppfiles/htdocs/update-php-field-names.php", 'rb') as f:
            ftp.storbinary('STOR update-php-field-names.php', f)
        print("✓ Uploaded: update-php-field-names.php")
        
        ftp.quit()
        
        print("\n🎉 STANDARDIZATION SCRIPTS UPLOADED!")
        
        print("\n" + "=" * 50)
        print("IMPORTANT: Follow these steps IN ORDER:")
        print("\n1. BACKUP YOUR DATABASE FIRST!")
        print("\n2. Run database standardization:")
        print("   https://11klassniki.ru/standardize-database-correct.php")
        print("   This will:")
        print("   - Rename meta_d_* → meta_description")
        print("   - Remove all meta_k_* fields")
        print("\n3. Update PHP files:")
        print("   https://11klassniki.ru/update-php-field-names.php")
        print("   This will update all PHP files to use new field names")
        print("\n4. Test the site thoroughly")
        print("=" * 50)
        
    except Exception as e:
        print(f"✗ Error: {str(e)}")
        return 1
    
    return 0

if __name__ == "__main__":
    sys.exit(main())