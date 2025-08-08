#!/usr/bin/env python3
"""
Upload batch 4 of migrated pages to production
"""

import os
import sys
from ftplib import FTP
from datetime import datetime

# FTP credentials
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_PATH = "/11klassnikiru"

# Files to upload
files_to_upload = [
    # New migrated pages
    "test-full-new.php",
    "search-process-new.php",
    
    # Updated .htaccess
    ".htaccess"
]

def upload_files():
    """Upload files to production server"""
    try:
        # Connect to FTP
        print(f"Connecting to {FTP_HOST}...")
        ftp = FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_PATH)
        print("Connected successfully!")
        
        success_count = 0
        error_count = 0
        
        for filename in files_to_upload:
            try:
                file_path = os.path.join('/Applications/XAMPP/xamppfiles/htdocs', filename)
                
                if not os.path.exists(file_path):
                    print(f"❌ File not found: {filename}")
                    error_count += 1
                    continue
                
                print(f"📤 Uploading {filename}...", end='', flush=True)
                
                with open(file_path, 'rb') as file:
                    ftp.storbinary(f'STOR {filename}', file)
                
                print(f" ✅ Success!")
                success_count += 1
                
            except Exception as e:
                print(f" ❌ Error: {str(e)}")
                error_count += 1
        
        # Close FTP connection
        ftp.quit()
        
        # Summary
        print("\n" + "="*50)
        print(f"Upload Summary:")
        print(f"✅ Successfully uploaded: {success_count} files")
        if error_count > 0:
            print(f"❌ Failed: {error_count} files")
        print("="*50)
        
        return success_count > 0
        
    except Exception as e:
        print(f"\n❌ FTP Connection Error: {str(e)}")
        return False

if __name__ == "__main__":
    print("Starting upload of batch 4 migrated pages...")
    print(f"Timestamp: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    print("="*50)
    
    if upload_files():
        print("\n✅ Upload completed!")
    else:
        print("\n❌ Upload failed!")
        sys.exit(1)