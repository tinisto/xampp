#!/usr/bin/env python3
"""
Upload unified template migration files to server
"""

import subprocess
import sys
from datetime import datetime

# FTP credentials
FTP_HOST = "11klassnikiru67871.ftp.tools"
FTP_USER = "11klassnikiru67871_main"
FTP_PASS = "$[59&}U4&+fH"
FTP_PORT = "21"

# Files to upload
files_to_upload = [
    # Modified files
    ".htaccess",
    "index.php",
    
    # New files
    "about-new.php",
    "category-new.php",
    "edu-single-new.php",
    "news-new.php",
    "post-new.php",
    "school-single-new.php",
    "schools-all-regions-real.php",
    "schools-in-region-real.php",
    "search-results-new.php",
    "spo-all-regions-new.php",
    "spo-in-region-new.php",
    "spo-single-new.php",
    "test-single-new.php",
    "tests-new.php",
    "vpo-all-regions-new.php",
    "vpo-all-regions-real.php",
    "vpo-in-region-new.php",
    "vpo-single-new.php",
    "write-new.php",
    
    # Modified pages
    "pages/category/category-new.php",
    "pages/common/news/news.php",
    "pages/post/post.php"
]

def upload_file(local_path, remote_path):
    """Upload a single file using curl"""
    print(f"Uploading {local_path} to {remote_path}...")
    
    cmd = [
        'curl',
        '-T', local_path,
        f'ftp://{FTP_HOST}:{FTP_PORT}/{remote_path}',
        '--user', f'{FTP_USER}:{FTP_PASS}',
        '--ftp-create-dirs',
        '--ftp-ssl',
        '--ftp-pasv'
    ]
    
    try:
        result = subprocess.run(cmd, capture_output=True, text=True, timeout=30)
        if result.returncode == 0:
            print(f"✓ Successfully uploaded {local_path}")
            return True
        else:
            print(f"✗ Failed to upload {local_path}: {result.stderr}")
            return False
    except subprocess.TimeoutExpired:
        print(f"✗ Timeout uploading {local_path}")
        return False
    except Exception as e:
        print(f"✗ Error uploading {local_path}: {str(e)}")
        return False

def main():
    print(f"Starting unified template upload at {datetime.now()}")
    print(f"Uploading {len(files_to_upload)} files...")
    print("-" * 50)
    
    success_count = 0
    failed_files = []
    
    for file_path in files_to_upload:
        # For files in subdirectories, maintain the directory structure
        remote_path = file_path
        local_path = file_path
        
        if upload_file(local_path, remote_path):
            success_count += 1
        else:
            failed_files.append(file_path)
    
    print("-" * 50)
    print(f"Upload complete: {success_count}/{len(files_to_upload)} files uploaded successfully")
    
    if failed_files:
        print("\nFailed uploads:")
        for f in failed_files:
            print(f"  - {f}")
        return 1
    
    print("\n✅ All files uploaded successfully!")
    print("\nNext steps:")
    print("1. Test homepage at https://11klassniki.ru")
    print("2. Test news at https://11klassniki.ru/news")
    print("3. Test schools at https://11klassniki.ru/schools-all-regions")
    print("4. Clear browser cache if needed")
    
    return 0

if __name__ == "__main__":
    sys.exit(main())