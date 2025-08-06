#!/usr/bin/env python3
"""
Upload Tests Main Content
=========================
Upload the tests content file
"""

import ftplib
import os

# FTP Configuration
FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'

def upload_file(ftp, local_path, remote_path):
    """Upload a single file via FTP"""
    try:
        with open(local_path, 'rb') as file:
            ftp.storbinary(f'STOR {remote_path}', file)
        print(f"✓ Uploaded: {local_path} -> {remote_path}")
        return True
    except Exception as e:
        print(f"✗ Failed to upload {local_path}: {e}")
        return False

def main():
    print("🚀 Upload Tests Content File")
    print("=" * 30)
    
    try:
        print(f"Connecting to {FTP_HOST}...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        
        local_path = '/Applications/XAMPP/xamppfiles/htdocs/pages/tests/tests-main-content.php'
        remote_path = '/11klassnikiru/pages/tests/tests-main-content.php'
        
        if os.path.exists(local_path):
            upload_file(ftp, local_path, remote_path)
        else:
            print(f"✗ Local file not found: {local_path}")
        
        ftp.quit()
        print("\n✓ Upload complete!")
        
    except Exception as e:
        print(f"Connection error: {e}")

if __name__ == "__main__":
    main()