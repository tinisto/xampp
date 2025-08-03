#!/usr/bin/env python3
"""Deploy VPO/SPO pages fix"""

import ftplib
from pathlib import Path

# FTP connection details
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
WEB_ROOT = "/11klassnikiru"

# Files to upload
files_to_upload = [
    "pages/common/educational-institutions-all-regions/educational-institutions-all-regions.php",
    "pages/common/educational-institutions-all-regions/educational-institutions-all-regions-content.php"
]

def main():
    print("🚀 Deploying VPO/SPO pages fix...")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        print(f"✅ Connected to {FTP_HOST}")
        
        for file in files_to_upload:
            local_path = Path(file)
            if local_path.exists():
                # Navigate to directory
                parts = file.split('/')
                remote_dir = WEB_ROOT + '/' + '/'.join(parts[:-1])
                ftp.cwd(remote_dir)
                
                # Upload file
                with open(local_path, 'rb') as f:
                    ftp.storbinary(f'STOR {parts[-1]}', f)
                print(f"✅ Uploaded: {file}")
            else:
                print(f"❌ File not found: {file}")
        
        ftp.quit()
        print("\n✅ FTP connection closed")
        print("\n🔧 The VPO/SPO pages have been fixed!")
        print("Test at:")
        print("- https://11klassniki.ru/vpo-all-regions")
        print("- https://11klassniki.ru/spo-all-regions")
        
    except Exception as e:
        print(f"❌ Error: {e}")
        return 1
    
    return 0

if __name__ == "__main__":
    exit(main())