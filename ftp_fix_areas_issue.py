#!/usr/bin/env python3
"""Re-upload the areas fix script"""

import ftplib
from pathlib import Path

# FTP connection details
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
WEB_ROOT = "/11klassnikiru"

# File to upload
file_to_upload = "fix_missing_records_with_areas.php"

def main():
    print("🚀 Re-uploading area fix script...")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        print(f"✅ Connected to {FTP_HOST}")
        
        # Change to web root
        ftp.cwd(WEB_ROOT)
        print(f"📁 In directory: {ftp.pwd()}")
        
        # Check if file exists locally
        local_path = Path(file_to_upload)
        if not local_path.exists():
            print(f"❌ File not found locally: {file_to_upload}")
            return 1
            
        # Upload file
        with open(local_path, 'rb') as f:
            ftp.storbinary(f'STOR {file_to_upload}', f)
        print(f"✅ Uploaded: {file_to_upload}")
        
        # Verify upload
        try:
            size = ftp.size(file_to_upload)
            print(f"✅ File size on server: {size} bytes")
        except:
            print("⚠️  Could not verify file size")
        
        # List all fix_ files
        print("\n📋 All fix_* files on server:")
        files = []
        ftp.retrlines('LIST fix_*.php', files.append)
        for f in files:
            print(f"  {f}")
        
        ftp.quit()
        print("\n✅ FTP connection closed")
        
    except Exception as e:
        print(f"❌ Error: {e}")
        return 1
    
    return 0

if __name__ == "__main__":
    exit(main())