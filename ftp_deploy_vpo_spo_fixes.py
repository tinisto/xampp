#!/usr/bin/env python3
"""Deploy VPO/SPO fixes to server"""

import ftplib
import os
from pathlib import Path

# FTP connection details
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

# Files to upload
files_to_upload = [
    # Educational institutions pages with fixes
    "pages/common/educational-institutions-all-regions/educational-institutions-all-regions.php",
    "pages/common/educational-institutions-all-regions/educational-institutions-all-regions-content.php",
    "pages/common/educational-institutions-in-region/educational-institutions-in-region.php",
    "pages/common/educational-institutions-in-region/educational-institutions-in-region-content.php",
    "pages/common/educational-institutions-in-region/outputEducationalInstitutions.php",
    "pages/common/educational-institutions-in-region/fetch-data-from-regions-table.php",
    
    # Test page
    "test_vpo_spo_pages.php",
    
    # Database test script
    "final_vpo_spo_fix.php"
]

def upload_file(ftp, local_path, remote_path):
    """Upload a single file to FTP server"""
    try:
        # Ensure directory exists
        remote_dir = os.path.dirname(remote_path)
        if remote_dir:
            try:
                ftp.cwd('/')
                parts = remote_dir.split('/')
                for part in parts:
                    if part:
                        try:
                            ftp.cwd(part)
                        except:
                            ftp.mkd(part)
                            ftp.cwd(part)
            except Exception as e:
                print(f"  ⚠️  Could not create directory {remote_dir}: {e}")
        
        # Upload file
        ftp.cwd('/')
        with open(local_path, 'rb') as f:
            ftp.storbinary(f'STOR {remote_path}', f)
        print(f"✅ Uploaded: {remote_path}")
        return True
        
    except Exception as e:
        print(f"❌ Failed to upload {remote_path}: {e}")
        return False

def main():
    print("🚀 Deploying VPO/SPO fixes to server...")
    
    # Connect to FTP
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        print(f"✅ Connected to {FTP_HOST}")
        
        # Change to web root
        try:
            ftp.cwd('/11klassniki.ru')
            print("✅ Changed to web root directory")
        except:
            print("⚠️  Could not change to /11klassniki.ru, using current directory")
        
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
        
    except Exception as e:
        print(f"❌ FTP connection error: {e}")
        return 1
    
    return 0

if __name__ == "__main__":
    exit(main())