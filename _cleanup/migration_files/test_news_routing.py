#!/usr/bin/env python3

import ftplib
import os

# FTP details
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

try:
    print("Connecting to FTP server...")
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd('/11klassnikiru')
    print("✓ Connected")
    
    # List all news files
    print("\nListing pages/news/ directory:")
    ftp.retrlines('LIST pages/news/')
    
    # Now let's upload the clean file over ALL possible news files
    local_file = 'pages/news/news-main-working.php'
    
    files_to_overwrite = [
        'pages/news/news.php',
        'pages/news/news-main.php',
        'pages/news/news-main-fixed.php'
    ]
    
    for remote_file in files_to_overwrite:
        try:
            with open(local_file, 'rb') as file:
                result = ftp.storbinary(f'STOR {remote_file}', file)
                print(f"✓ Uploaded clean version to: {remote_file}")
        except Exception as e:
            print(f"✗ Failed to upload to {remote_file}: {e}")
    
    ftp.quit()
    print("\n✓ All news files updated!")
    
except Exception as e:
    print(f"FTP Error: {e}")