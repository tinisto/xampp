#!/usr/bin/env python3
"""Download and check the production .env file"""

import ftplib
import io

FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
FTP_DIR = '11klassnikiru'

try:
    # Connect
    ftp = ftplib.FTP(FTP_HOST, timeout=30)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(FTP_DIR)
    
    # Download .env file
    env_content = []
    try:
        ftp.retrlines('RETR .env', env_content.append)
        print("Current .env file contents:")
        print("-" * 50)
        for line in env_content:
            if 'PASS' in line or 'PASSWORD' in line:
                parts = line.split('=', 1)
                if len(parts) == 2:
                    print(f"{parts[0]}=***hidden***")
                else:
                    print(line)
            else:
                print(line)
        print("-" * 50)
    except Exception as e:
        print(f"Error reading .env: {e}")
    
    # Check if there's a backup
    files = []
    ftp.retrlines('NLST .env*', files.append)
    print("\n.env files on server:")
    for f in files:
        print(f"  - {f}")
    
    ftp.quit()
    
except Exception as e:
    print(f"Error: {e}")