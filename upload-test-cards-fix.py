#!/usr/bin/env python3

import ftplib
import os
import sys
from datetime import datetime

def create_ftp_connection():
    """Create an FTP connection."""
    ftp = ftplib.FTP()
    ftp.set_debuglevel(0)
    ftp.connect('77.232.131.89', 21, timeout=30)
    ftp.login('8b6cdc76_sitearchive', 'jU9%mHr1')
    ftp.cwd('/domains/11klassniki.ru/public_html')
    return ftp

def upload_files(ftp):
    """Upload the test card files."""
    files_to_upload = [
        'common-components/test-card.php',
        'pages/tests/tests-main-content.php'
    ]
    
    for file_path in files_to_upload:
        local_file = os.path.join('/Applications/XAMPP/xamppfiles/htdocs', file_path)
        
        if os.path.exists(local_file):
            print(f"Uploading {file_path}...")
            
            try:
                with open(local_file, 'rb') as f:
                    ftp.storbinary(f'STOR {file_path}', f)
                print(f"✓ Successfully uploaded {file_path}")
            except Exception as e:
                print(f"✗ Error uploading {file_path}: {e}")
        else:
            print(f"✗ File not found: {local_file}")

def main():
    print(f"\n=== Uploading test card updates at {datetime.now().strftime('%Y-%m-%d %H:%M:%S')} ===\n")
    
    try:
        ftp = create_ftp_connection()
        print("✓ Connected to FTP server")
        
        upload_files(ftp)
        
        ftp.quit()
        print("\n✓ FTP connection closed")
        print("\n=== Upload completed successfully ===")
        
    except Exception as e:
        print(f"\n✗ Error: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()