#!/usr/bin/env python3
"""
Template for uploading files to 11klassniki.ru
Usage: python3 upload_template.py [file1] [file2] ...
"""

import ftplib
import os
import sys
from .ftp_credentials import get_ftp_config

def upload_files(files):
    """Upload files to server"""
    config = get_ftp_config()
    
    try:
        # Connect to FTP
        print(f"Connecting to {config['host']}...")
        ftp = ftplib.FTP(config['host'])
        ftp.login(config['user'], config['pass'])
        
        # Change to website directory
        ftp.cwd(config['root'])
        
        # Upload files
        for file in files:
            if os.path.exists(file):
                print(f"Uploading {file}...")
                with open(file, 'rb') as f:
                    ftp.storbinary(f'STOR {os.path.basename(file)}', f)
                print(f"✅ Uploaded {file}")
            else:
                print(f"❌ File not found: {file}")
        
        ftp.quit()
        print("\n✅ Upload complete!")
        
    except Exception as e:
        print(f"❌ Error: {e}")

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print("Usage: python3 upload_template.py [file1] [file2] ...")
        sys.exit(1)
    
    files = sys.argv[1:]
    upload_files(files)