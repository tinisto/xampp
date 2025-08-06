#!/usr/bin/env python3
"""
Upload Template Fixes
====================
Remove test colors from template
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
        print(f"✓ Uploaded: {os.path.basename(local_path)}")
        return True
    except Exception as e:
        print(f"✗ Failed to upload {os.path.basename(local_path)}: {e}")
        return False

def main():
    print("🚀 Upload Template Fixes")
    print("=" * 30)
    
    files_to_upload = [
        {
            'local': '/Applications/XAMPP/xamppfiles/htdocs/common-components/template-engine-ultimate.php',
            'remote': '/11klassnikiru/common-components/template-engine-ultimate.php'
        },
        {
            'local': '/Applications/XAMPP/xamppfiles/htdocs/common-components/content-wrapper.php',
            'remote': '/11klassnikiru/common-components/content-wrapper.php'
        },
        {
            'local': '/Applications/XAMPP/xamppfiles/htdocs/pages/tests/tests-main-content.php',
            'remote': '/11klassnikiru/pages/tests/tests-main-content.php'
        }
    ]
    
    try:
        print(f"Connecting to {FTP_HOST}...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        
        for file_info in files_to_upload:
            upload_file(ftp, file_info['local'], file_info['remote'])
        
        ftp.quit()
        print("\n✓ Upload complete!")
        
    except Exception as e:
        print(f"Connection error: {e}")

if __name__ == "__main__":
    main()