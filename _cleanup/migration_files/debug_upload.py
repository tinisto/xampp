#!/usr/bin/env python3

import ftplib
import os

# FTP details
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"

def upload_file(ftp, local_path, remote_path):
    try:
        print(f"Uploading {local_path} to {remote_path}...")
        
        # Check if local file exists
        if not os.path.exists(local_path):
            print(f"ERROR: Local file {local_path} does not exist!")
            return False
            
        # Get file size
        file_size = os.path.getsize(local_path)
        print(f"Local file size: {file_size} bytes")
        
        with open(local_path, 'rb') as file:
            result = ftp.storbinary(f'STOR {remote_path}', file)
            print(f"FTP response: {result}")
            
        print(f'✓ Successfully uploaded: {local_path} -> {remote_path}')
        return True
    except Exception as e:
        print(f'✗ Failed to upload {local_path}: {str(e)}')
        return False

try:
    print("Connecting to FTP server...")
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd('/11klassnikiru')
    print("✓ Connected and changed to web root")
    
    # Upload the news file
    local_file = 'pages/news/news-main-working.php'
    remote_file = 'pages/news/news.php'
    
    upload_file(ftp, local_file, remote_file)
    
    ftp.quit()
    print("\n✓ FTP connection closed")
    print("\nNews page should now be updated without dates!")
    
except Exception as e:
    print(f"FTP Error: {e}")