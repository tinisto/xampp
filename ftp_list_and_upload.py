#!/usr/bin/env python3
"""List files and upload migration scripts"""

import ftplib
from pathlib import Path

# FTP connection details
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
WEB_ROOT = "/11klassnikiru"

# Files to upload
files_to_upload = [
    "fix_missing_records.php",
    "fix_missing_records_v2.php", 
    "fix_missing_records_final.php",
    "fix_missing_records_with_areas.php"
]

def main():
    print("🚀 Checking and uploading migration scripts...")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        print(f"✅ Connected to {FTP_HOST}")
        
        # Change to web root
        ftp.cwd(WEB_ROOT)
        print(f"📁 Changed to: {WEB_ROOT}")
        
        # List existing fix_* files
        print("\n📋 Existing fix_* files on server:")
        files = []
        ftp.retrlines('LIST fix_*.php', files.append)
        for f in files:
            print(f"  {f}")
        
        if not files:
            print("  No fix_*.php files found")
            
        # Upload all migration scripts
        print("\n📤 Uploading migration scripts...")
        for file_name in files_to_upload:
            local_path = Path(file_name)
            if local_path.exists():
                try:
                    with open(local_path, 'rb') as f:
                        ftp.storbinary(f'STOR {file_name}', f)
                    print(f"✅ Uploaded: {file_name}")
                except Exception as e:
                    print(f"❌ Failed to upload {file_name}: {e}")
            else:
                print(f"⚠️  File not found locally: {file_name}")
        
        # Verify uploads
        print("\n📋 Verifying uploads:")
        files = []
        ftp.retrlines('LIST fix_*.php', files.append)
        for f in files:
            print(f"  {f}")
        
        ftp.quit()
        print("\n✅ FTP connection closed")
        
        print("\n🌐 Available migration scripts:")
        print("1. https://11klassniki.ru/fix_missing_records.php")
        print("2. https://11klassniki.ru/fix_missing_records_v2.php")
        print("3. https://11klassniki.ru/fix_missing_records_final.php")
        print("4. https://11klassniki.ru/fix_missing_records_with_areas.php (RECOMMENDED)")
        
    except Exception as e:
        print(f"❌ Error: {e}")
        return 1
    
    return 0

if __name__ == "__main__":
    exit(main())