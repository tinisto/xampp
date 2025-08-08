#!/usr/bin/env python3
"""Upload all unified template system files"""

import ftplib
import os
import sys

# FTP credentials
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_PATH = "/11klassnikiru/"

# Files to upload - including all dependencies
files_to_upload = [
    # Core template files
    "real_template.php",
    "real_components.php",
    
    # Core modified files
    ".htaccess",
    "index.php",
    
    # Reusable components used by template
    "common-components/real_title.php",
    "common-components/real_header.php",
    "common-components/real_footer.php",
    "common-components/search-inline.php",
    "common-components/cards-grid.php",
    "common-components/category-navigation.php",
    "common-components/filters-dropdown.php",
    "common-components/pagination-modern.php",
    "common-components/breadcrumb.php",
    
    # New unified template pages
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
    "search-results-new.php"
]

def upload_file(ftp, local_file, remote_file):
    """Upload a single file"""
    try:
        # Ensure directory exists
        remote_dir = os.path.dirname(remote_file)
        if remote_dir:
            try:
                ftp.cwd(FTP_PATH)
                for folder in remote_dir.split('/'):
                    if folder:
                        try:
                            ftp.cwd(folder)
                        except:
                            ftp.mkd(folder)
                            ftp.cwd(folder)
                ftp.cwd(FTP_PATH)
            except:
                pass
        
        with open(local_file, 'rb') as f:
            ftp.storbinary(f'STOR {remote_file}', f)
        print(f"✓ Uploaded: {remote_file}")
        return True
    except Exception as e:
        print(f"✗ Error uploading {remote_file}: {e}")
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
                # Don't count as failed if file doesn't exist locally
        
        # Close connection
        ftp.quit()
        
        # Summary
        print("\n" + "="*50)
        print(f"Upload complete: {success_count} files uploaded")
        
        if failed_files:
            print("\nFailed uploads:")
            for f in failed_files:
                print(f"  - {f}")
        else:
            print("\n✅ All existing files uploaded successfully!")
            
        print("\nIMPORTANT: Clear browser cache and test:")
        print("- https://11klassniki.ru")
        print("- https://11klassniki.ru/schools-all-regions")
            
        return 0 if not failed_files else 1
        
    except Exception as e:
        print(f"✗ FTP connection error: {e}")
        return 1

if __name__ == "__main__":
    sys.exit(main())