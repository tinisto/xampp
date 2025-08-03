#!/usr/bin/env python3
"""Upload debug files to correct web root"""

import ftplib
from pathlib import Path

# FTP connection details
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
WEB_ROOT = "/11klassnikiru"

# Files to upload
files_to_upload = [
    "debug_regions_vpo.php",
    "debug_spo_all_regions.php",
    "test_vpo_spo_pages.php"
]

def main():
    print("🚀 Uploading debug files to correct location...")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        print(f"✅ Connected to {FTP_HOST}")
        
        # Change to web root
        ftp.cwd(WEB_ROOT)
        print(f"📁 Changed to web root: {WEB_ROOT}")
        
        # Upload files
        success_count = 0
        for file_name in files_to_upload:
            local_path = Path(file_name)
            if local_path.exists():
                try:
                    with open(local_path, 'rb') as f:
                        ftp.storbinary(f'STOR {file_name}', f)
                    print(f"✅ Uploaded: {file_name}")
                    success_count += 1
                except Exception as e:
                    print(f"❌ Failed to upload {file_name}: {e}")
            else:
                print(f"⚠️  File not found locally: {file_name}")
        
        print(f"\n📊 Upload complete: {success_count}/{len(files_to_upload)} files uploaded")
        
        # Verify files
        print("\n📋 Verifying uploaded files:")
        for file_name in files_to_upload:
            try:
                size = ftp.size(file_name)
                if size:
                    print(f"  ✅ {file_name} ({size} bytes)")
                else:
                    print(f"  ❌ {file_name} not found")
            except:
                print(f"  ❌ {file_name} not found")
        
        ftp.quit()
        print("\n✅ FTP connection closed")
        
    except Exception as e:
        print(f"❌ Error: {e}")
        return 1
    
    return 0

if __name__ == "__main__":
    exit(main())