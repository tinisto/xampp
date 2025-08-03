#!/usr/bin/env python3
"""Urgent database connection fix"""

import ftplib
from pathlib import Path

# FTP connection details
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
WEB_ROOT = "/11klassnikiru"

# Files to upload
files_to_upload = [
    "fix_db_connection_urgent.php",
    ".env",  # Re-upload the corrected .env file
    "database/db_connections.php"  # Make sure this has correct settings
]

def main():
    print("🚨 URGENT: Fixing database connection issue...")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        print(f"✅ Connected to {FTP_HOST}")
        
        # Change to web root
        ftp.cwd(WEB_ROOT)
        
        # Upload files
        for file_path in files_to_upload:
            local_path = Path(file_path)
            if local_path.exists():
                try:
                    # Handle subdirectories
                    if '/' in file_path:
                        parts = file_path.split('/')
                        remote_dir = '/'.join(parts[:-1])
                        # Navigate to directory
                        ftp.cwd(WEB_ROOT)
                        for part in parts[:-1]:
                            try:
                                ftp.cwd(part)
                            except:
                                pass
                        # Upload file
                        with open(local_path, 'rb') as f:
                            ftp.storbinary(f'STOR {parts[-1]}', f)
                        ftp.cwd(WEB_ROOT)
                    else:
                        with open(local_path, 'rb') as f:
                            ftp.storbinary(f'STOR {file_path}', f)
                    print(f"✅ Uploaded: {file_path}")
                except Exception as e:
                    print(f"❌ Failed to upload {file_path}: {e}")
            else:
                print(f"⚠️  File not found locally: {file_path}")
        
        ftp.quit()
        print("\n✅ FTP connection closed")
        print("\n🚨 URGENT STEPS:")
        print("1. Visit: https://11klassniki.ru/fix_db_connection_urgent.php")
        print("2. Click 'FIX DATABASE CONNECTION NOW'")
        print("3. The site may need a PHP restart to pick up the new settings")
        
    except Exception as e:
        print(f"❌ Error: {e}")
        return 1
    
    return 0

if __name__ == "__main__":
    exit(main())