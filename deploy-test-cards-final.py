#!/usr/bin/env python3
"""Deploy test cards update to production server."""

import ftplib
import os
import sys
from datetime import datetime
import time

def create_ftp_connection(max_retries=3):
    """Create an FTP connection with retries."""
    for attempt in range(max_retries):
        try:
            print(f"Attempting FTP connection (attempt {attempt + 1}/{max_retries})...")
            ftp = ftplib.FTP()
            ftp.set_debuglevel(1)  # Enable debug output
            ftp.connect('77.232.131.89', 21, timeout=10)
            ftp.login('8b6cdc76_sitearchive', 'jU9%mHr1')
            ftp.cwd('/domains/11klassniki.ru/public_html')
            print("✓ Connected to FTP server")
            return ftp
        except Exception as e:
            print(f"✗ Connection attempt {attempt + 1} failed: {e}")
            if attempt < max_retries - 1:
                print(f"Waiting 5 seconds before retry...")
                time.sleep(5)
            else:
                raise
    return None

def upload_file(ftp, local_path, remote_path):
    """Upload a single file."""
    try:
        # Ensure we're in the right directory
        ftp.cwd('/domains/11klassniki.ru/public_html')
        
        # Create directory if needed
        remote_dir = os.path.dirname(remote_path)
        if remote_dir:
            dirs = remote_dir.split('/')
            for d in dirs:
                if d:
                    try:
                        ftp.cwd(d)
                    except:
                        try:
                            ftp.mkd(d)
                            ftp.cwd(d)
                        except:
                            pass
            ftp.cwd('/domains/11klassniki.ru/public_html')
        
        # Upload the file
        with open(local_path, 'rb') as f:
            ftp.storbinary(f'STOR {remote_path}', f)
        return True
    except Exception as e:
        print(f"Error uploading {remote_path}: {e}")
        return False

def main():
    """Main deployment function."""
    print(f"\n{'='*60}")
    print(f"Test Cards Deployment - {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    print(f"{'='*60}\n")
    
    # Files to upload
    files_to_upload = [
        ('common-components/test-card.php', 'common-components/test-card.php'),
        ('pages/tests/tests-main-content.php', 'pages/tests/tests-main-content.php')
    ]
    
    # Check if files exist locally
    print("Checking local files...")
    for local_file, _ in files_to_upload:
        full_path = f'/Applications/XAMPP/xamppfiles/htdocs/{local_file}'
        if os.path.exists(full_path):
            size = os.path.getsize(full_path)
            print(f"✓ {local_file} ({size} bytes)")
        else:
            print(f"✗ {local_file} NOT FOUND")
            sys.exit(1)
    
    print("\nConnecting to FTP server...")
    
    try:
        ftp = create_ftp_connection()
        if not ftp:
            raise Exception("Failed to establish FTP connection")
        
        print("\nUploading files...")
        success_count = 0
        
        for local_file, remote_file in files_to_upload:
            full_local_path = f'/Applications/XAMPP/xamppfiles/htdocs/{local_file}'
            print(f"\nUploading {local_file}...")
            
            if upload_file(ftp, full_local_path, remote_file):
                print(f"✓ Successfully uploaded {remote_file}")
                success_count += 1
            else:
                print(f"✗ Failed to upload {remote_file}")
        
        print(f"\n{'='*60}")
        print(f"Deployment complete: {success_count}/{len(files_to_upload)} files uploaded")
        print(f"{'='*60}")
        
        # Close connection
        try:
            ftp.quit()
        except:
            ftp.close()
        
        if success_count == len(files_to_upload):
            print("\n✓ All files uploaded successfully!")
            print("\nPlease check https://11klassniki.ru/tests")
        else:
            print("\n⚠ Some files failed to upload")
            sys.exit(1)
            
    except Exception as e:
        print(f"\n✗ Deployment failed: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()