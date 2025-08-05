#!/usr/bin/env python3

import ftplib
import os

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

def upload_safe_replacements():
    print("🚀 Uploading safe field replacements guide")
    
    try:
        print("🔌 Connecting...")
        ftp = ftplib.FTP()
        ftp.connect(HOST, 21, timeout=30)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✅ Connected")
        
        # Upload safe-field-replacements.php
        local_path = '/Applications/XAMPP/xamppfiles/htdocs/safe-field-replacements.php'
        if os.path.exists(local_path):
            with open(local_path, 'rb') as f:
                ftp.storbinary('STOR safe-field-replacements.php', f)
            print("✅ safe-field-replacements.php uploaded")
        else:
            print("❌ File not found: safe-field-replacements.php")
            return False
        
        ftp.quit()
        
        print("\n🎉 Safe replacements guide uploaded!")
        print("\n📋 View it at:")
        print("https://11klassniki.ru/safe-field-replacements.php")
        print("\n⚠️ IMPORTANT: This shows which replacements are SAFE vs DANGEROUS")
        print("\n✅ Safe replacements (for fields we migrated):")
        print("- id_entity → entity_id")
        print("- id_vpo → vpo_id") 
        print("- id_spo → spo_id")
        print("- id_school → school_id")
        print("- id_rono → rono_id")
        print("- id_indeks → indeks_id")
        print("- id_country → country_id")
        print("\n❌ DANGEROUS replacements (would break foreign keys):")
        print("- region_id → id")
        print("- area_id → id")
        print("- country_id → id (as primary key)")
        print("- town_id → id")
        
        return True
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        return False

if __name__ == "__main__":
    upload_safe_replacements()