#!/usr/bin/env python3
"""Check FTP directory and upload debug files"""

import ftplib
from pathlib import Path

# FTP connection details
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

# Files to upload
files_to_upload = [
    "debug_regions_vpo.php",
    "debug_spo_all_regions.php",
    "test_vpo_spo_pages.php"
]

def main():
    print("🚀 Checking FTP and uploading debug files...")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        print(f"✅ Connected to {FTP_HOST}")
        
        # Check current directory
        pwd = ftp.pwd()
        print(f"📁 Current directory: {pwd}")
        
        # List files in root
        print("\n📋 Files in current directory:")
        files = []
        ftp.dir(files.append)
        for f in files[:10]:  # Show first 10
            print(f"  {f}")
        
        # Try to find web root
        possible_roots = ['www', 'public_html', 'htdocs', '11klassniki.ru']
        web_root = None
        
        for root in possible_roots:
            try:
                ftp.cwd(root)
                web_root = root
                print(f"\n✅ Found web root: {root}")
                break
            except:
                ftp.cwd(pwd)  # Go back to original
                
        if not web_root:
            print("\n⚠️  Could not find web root, uploading to current directory")
        
        # Upload files
        print("\n📤 Uploading files...")
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
        
        # List uploaded files
        print("\n📋 Verifying uploaded files:")
        ftp.retrlines('LIST *.php', lambda x: print(f"  {x}") if any(f in x for f in files_to_upload) else None)
        
        ftp.quit()
        print("\n✅ FTP connection closed")
        
    except Exception as e:
        print(f"❌ Error: {e}")
        return 1
    
    return 0

if __name__ == "__main__":
    exit(main())