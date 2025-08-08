#!/usr/bin/env python3
"""Upload unified template files using franko FTP account"""

import ftplib
import os
import sys

# FTP credentials
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_PATH = "/11klassnikiru/"

# Files to upload
files_to_upload = [
    # Core files
    ".htaccess",
    "index.php",
    
    # New unified template files
    "school-single-new.php",
    "schools-all-regions-real.php",
    "schools-in-region-real.php",
    "spo-single-new.php",
    "spo-all-regions-new.php",
    "spo-in-region-new.php",
    "vpo-single-new.php",
    "vpo-all-regions-new.php",
    "vpo-in-region-new.php",
    "test-single-new.php",
    "tests-new.php",
    "search-results-new.php",
    "edu-single-new.php"
]

def upload_file(ftp, local_file, remote_file):
    """Upload a single file"""
    try:
        with open(local_file, 'rb') as f:
            ftp.storbinary(f'STOR {remote_file}', f)
        print(f"✓ Uploaded: {remote_file}")
        return True
    except Exception as e:
        print(f"✗ Error uploading {remote_file}: {e}")
        return False

def ensure_directory(ftp, directory):
    """Ensure directory exists"""
    try:
        ftp.cwd(directory)
        return True
    except:
        try:
            ftp.mkd(directory)
            ftp.cwd(directory)
            return True
        except:
            return False

def main():
    """Main upload function"""
    try:
        # Connect to FTP
        print("Connecting to FTP server...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_PATH)
        print("✓ Connected successfully")
        
        # Upload files
        success_count = 0
        failed_files = []
        
        for file_path in files_to_upload:
            if os.path.exists(file_path):
                if upload_file(ftp, file_path, file_path):
                    success_count += 1
                else:
                    failed_files.append(file_path)
            else:
                print(f"✗ File not found: {file_path}")
                failed_files.append(file_path)
        
        # Close connection
        ftp.quit()
        
        # Summary
        print("\n" + "="*50)
        print(f"Upload complete: {success_count}/{len(files_to_upload)} files uploaded")
        
        if failed_files:
            print("\nFailed uploads:")
            for f in failed_files:
                print(f"  - {f}")
        else:
            print("\n✅ All files uploaded successfully!")
            
        return 0 if not failed_files else 1
        
    except Exception as e:
        print(f"✗ FTP connection error: {e}")
        return 1

if __name__ == "__main__":
    sys.exit(main())