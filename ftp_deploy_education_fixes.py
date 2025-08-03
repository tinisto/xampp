#!/usr/bin/env python3
"""Deploy educational institutions fixes to correct location"""

import ftplib
import os
from pathlib import Path

# FTP connection details
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
WEB_ROOT = "/11klassnikiru"

# Files to upload
files_to_upload = [
    # Main educational institutions pages
    "pages/common/educational-institutions-all-regions/educational-institutions-all-regions.php",
    "pages/common/educational-institutions-all-regions/educational-institutions-all-regions-content.php",
    "pages/common/educational-institutions-in-region/educational-institutions-in-region.php",
    "pages/common/educational-institutions-in-region/educational-institutions-in-region-content.php",
    "pages/common/educational-institutions-in-region/outputEducationalInstitutions.php",
    "pages/common/educational-institutions-in-region/fetch-data-from-regions-table.php",
]

def ensure_directory(ftp, path):
    """Create directory structure if it doesn't exist"""
    parts = path.split('/')
    current = ""
    
    for part in parts:
        if part:
            current = f"{current}/{part}" if current else part
            try:
                ftp.cwd(f"/{current}")
            except:
                try:
                    ftp.mkd(part)
                    ftp.cwd(part)
                except:
                    pass

def upload_file(ftp, local_path, remote_path):
    """Upload a single file to FTP server"""
    try:
        # Ensure directory exists
        remote_dir = os.path.dirname(remote_path)
        if remote_dir:
            ftp.cwd(WEB_ROOT)
            ensure_directory(ftp, remote_dir)
        
        # Upload file
        ftp.cwd(WEB_ROOT)
        with open(local_path, 'rb') as f:
            full_remote_path = f"{WEB_ROOT}/{remote_path}"
            ftp.storbinary(f'STOR {full_remote_path}', f)
        print(f"✅ Uploaded: {remote_path}")
        return True
        
    except Exception as e:
        print(f"❌ Failed to upload {remote_path}: {e}")
        return False

def main():
    print("🚀 Deploying educational institutions fixes...")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        print(f"✅ Connected to {FTP_HOST}")
        
        # Upload files
        success_count = 0
        total_count = len(files_to_upload)
        
        for file_path in files_to_upload:
            local_path = Path(file_path)
            if local_path.exists():
                if upload_file(ftp, local_path, file_path):
                    success_count += 1
            else:
                print(f"⚠️  File not found locally: {file_path}")
        
        print(f"\n📊 Upload complete: {success_count}/{total_count} files uploaded successfully")
        
        # Close connection
        ftp.quit()
        print("✅ FTP connection closed")
        
        print("\n🌐 Test the pages:")
        print("  - https://11klassniki.ru/vpo-all-regions")
        print("  - https://11klassniki.ru/spo-all-regions")
        print("  - https://11klassniki.ru/schools-all-regions")
        
    except Exception as e:
        print(f"❌ FTP connection error: {e}")
        return 1
    
    return 0

if __name__ == "__main__":
    exit(main())