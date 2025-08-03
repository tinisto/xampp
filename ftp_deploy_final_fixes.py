#!/usr/bin/env python3
"""Deploy final database migration fixes"""

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
    # Migration script with correct column names
    "fix_missing_records_final.php",
    
    # Updated page with correct column names
    "pages/common/educational-institutions-in-region/educational-institutions-in-region.php"
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
        # For simple files in root
        if '/' not in remote_path:
            ftp.cwd(WEB_ROOT)
            with open(local_path, 'rb') as f:
                ftp.storbinary(f'STOR {remote_path}', f)
        else:
            # For files in subdirectories
            remote_dir = os.path.dirname(remote_path)
            if remote_dir:
                ftp.cwd(WEB_ROOT)
                ensure_directory(ftp, remote_dir)
            
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
    print("🚀 Deploying final database migration fixes...")
    
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
        
        print("\n📋 Next steps:")
        print("1. Visit https://11klassniki.ru/fix_missing_records_final.php to migrate records")
        print("2. The migration will process records in batches of 50")
        print("3. Keep clicking 'Continue Migration' until all records are processed")
        
    except Exception as e:
        print(f"❌ FTP connection error: {e}")
        return 1
    
    return 0

if __name__ == "__main__":
    exit(main())