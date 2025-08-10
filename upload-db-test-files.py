#!/usr/bin/env python3
import ftplib
import os
from datetime import datetime

# FTP credentials
FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
FTP_ROOT = '/11klassnikiru'

# Files to upload
files_to_upload = [
    'check-db-tables-complete.php'
]

def upload_files():
    """Upload test files to production server"""
    try:
        # Connect to FTP
        print(f"Connecting to {FTP_HOST}...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        print("✓ Connected successfully")
        
        # Change to root directory
        ftp.cwd(FTP_ROOT)
        print(f"✓ Changed to directory: {FTP_ROOT}")
        
        # Upload each file
        uploaded = 0
        for filename in files_to_upload:
            local_path = f"/Applications/XAMPP/xamppfiles/htdocs/{filename}"
            
            if os.path.exists(local_path):
                try:
                    with open(local_path, 'rb') as f:
                        ftp.storbinary(f'STOR {filename}', f)
                    print(f"✓ Uploaded: {filename}")
                    uploaded += 1
                except Exception as e:
                    print(f"✗ Error uploading {filename}: {e}")
            else:
                print(f"✗ File not found: {filename}")
        
        # Close connection
        ftp.quit()
        print(f"\n✓ Upload complete! {uploaded}/{len(files_to_upload)} files uploaded")
        print(f"\nTest URL: https://11klassniki.ru/check-db-tables-complete.php")
        
    except Exception as e:
        print(f"✗ FTP Error: {e}")

if __name__ == "__main__":
    print("=== Uploading Database Test Files ===")
    print(f"Time: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}\n")
    upload_files()