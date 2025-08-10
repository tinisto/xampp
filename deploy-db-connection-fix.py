#!/usr/bin/env python3
import ftplib
import os
from datetime import datetime

# FTP credentials
FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
FTP_ROOT = '/11klassnikiru'

# Files to upload
files_to_upload = [
    ('database/db_connections_fixed.php', 'database/db_connections_fixed.php'),
    ('database/db_connections_simple.php', 'database/db_connections_simple.php'),
    ('database/db_connections.php', 'database/db_connections_backup.php'),  # Backup current
    ('database/db_connections_simple.php', 'database/db_connections.php')    # Replace with simple version
]

def upload_files():
    """Upload database connection fixes"""
    try:
        print(f"Connecting to {FTP_HOST}...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        print("✓ Connected successfully")
        
        # Change to root directory
        ftp.cwd(FTP_ROOT)
        
        # Create database directory if needed
        try:
            ftp.cwd('database')
            ftp.cwd('..')  # Go back to root
        except:
            print("Creating database directory...")
            ftp.mkd('database')
        
        # Upload each file
        uploaded = 0
        for local_file, remote_file in files_to_upload:
            local_path = f"/Applications/XAMPP/xamppfiles/htdocs/{local_file}"
            
            if os.path.exists(local_path):
                try:
                    # Navigate to directory if needed
                    if '/' in remote_file:
                        dir_name = os.path.dirname(remote_file)
                        ftp.cwd(FTP_ROOT + '/' + dir_name)
                        remote_name = os.path.basename(remote_file)
                    else:
                        ftp.cwd(FTP_ROOT)
                        remote_name = remote_file
                    
                    with open(local_path, 'rb') as f:
                        ftp.storbinary(f'STOR {remote_name}', f)
                    print(f"✓ Uploaded: {local_file} → {remote_file}")
                    uploaded += 1
                    
                except Exception as e:
                    print(f"✗ Error uploading {local_file}: {e}")
            else:
                print(f"✗ File not found: {local_file}")
        
        # Close connection
        ftp.quit()
        print(f"\n✓ Upload complete! {uploaded}/{len(files_to_upload)} operations completed")
        print(f"\nThe database connection has been updated to not redirect on errors.")
        print(f"Check: https://11klassniki.ru/news")
        
    except Exception as e:
        print(f"✗ FTP Error: {e}")

if __name__ == "__main__":
    print("=== Deploying Database Connection Fix ===")
    print(f"Time: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}\n")
    upload_files()