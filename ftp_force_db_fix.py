#!/usr/bin/env python3
"""Force correct database connection"""

import ftplib
from pathlib import Path

# FTP connection details
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
WEB_ROOT = "/11klassnikiru"

# Files to upload
files_to_upload = [
    "force_correct_db.php",
    "database/db_connections.php"  # With force flag enabled
]

def main():
    print("🚨 Forcing correct database connection...")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        print(f"✅ Connected to {FTP_HOST}")
        
        # Upload force_correct_db.php
        ftp.cwd(WEB_ROOT)
        local_path = Path("force_correct_db.php")
        if local_path.exists():
            with open(local_path, 'rb') as f:
                ftp.storbinary('STOR force_correct_db.php', f)
            print("✅ Uploaded: force_correct_db.php")
        
        # Upload db_connections.php
        ftp.cwd(WEB_ROOT + "/database")
        local_path = Path("database/db_connections.php")
        if local_path.exists():
            with open(local_path, 'rb') as f:
                ftp.storbinary('STOR db_connections.php', f)
            print("✅ Uploaded: database/db_connections.php (with force flag ENABLED)")
        
        ftp.quit()
        print("\n✅ FTP connection closed")
        print("\n✅ The force flag has been ENABLED!")
        print("The site should now use the correct database (11klassniki_claude)")
        print("\nVerify at: https://11klassniki.ru/site_review.php")
        
    except Exception as e:
        print(f"❌ Error: {e}")
        return 1
    
    return 0

if __name__ == "__main__":
    exit(main())