#!/usr/bin/env python3
import ftplib
import os
import sys
from datetime import datetime

# FTP connection details
FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
FTP_ROOT = '/11klassnikiru'

def upload_file_ftp(local_path, remote_path, ftp):
    """Upload a file via FTP"""
    try:
        # Ensure the remote directory exists
        remote_dir = os.path.dirname(remote_path)
        if remote_dir and remote_dir != '/':
            try:
                ftp.cwd(remote_dir)
            except:
                # Directory might not exist, create it
                create_remote_directory(ftp, remote_dir)
                ftp.cwd(remote_dir)
        
        # Upload the file
        with open(local_path, 'rb') as file:
            ftp.storbinary(f'STOR {os.path.basename(remote_path)}', file)
        
        print(f"✓ Uploaded: {local_path} -> {remote_path}")
        return True
    except Exception as e:
        print(f"✗ Failed to upload {local_path}: {str(e)}")
        return False

def create_remote_directory(ftp, path):
    """Create remote directory recursively"""
    dirs = path.strip('/').split('/')
    current_dir = ''
    
    for dir in dirs:
        current_dir += '/' + dir
        try:
            ftp.cwd(current_dir)
        except:
            try:
                ftp.mkd(current_dir)
                print(f"  Created directory: {current_dir}")
            except Exception as e:
                print(f"  Failed to create directory {current_dir}: {str(e)}")

def backup_file(ftp, remote_path):
    """Create a backup of the remote file"""
    try:
        # Generate backup filename with timestamp
        timestamp = datetime.now().strftime('%Y%m%d_%H%M%S')
        backup_name = f"{remote_path}.backup_{timestamp}"
        
        # Try to rename the file as backup
        ftp.rename(remote_path, backup_name)
        print(f"  Backed up: {remote_path} -> {backup_name}")
        return True
    except Exception as e:
        # File might not exist, which is fine
        if "550" not in str(e):  # 550 is "file not found" error
            print(f"  Backup info: {str(e)}")
        return False

def main():
    print("=== Database Connection Fix Upload ===")
    print(f"Connecting to {FTP_HOST}...")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        print(f"✓ Connected to FTP server")
        
        # Change to root directory
        ftp.cwd(FTP_ROOT)
        print(f"✓ Changed to root directory: {FTP_ROOT}")
        
        # Files to upload
        files_to_upload = [
            {
                'local': '/Applications/XAMPP/xamppfiles/htdocs/.env',
                'remote': f'{FTP_ROOT}/.env',
                'backup': True
            },
            {
                'local': '/Applications/XAMPP/xamppfiles/htdocs/database/db_connections_restored.php',
                'remote': f'{FTP_ROOT}/database/db_connections.php',
                'backup': True
            },
            {
                'local': '/Applications/XAMPP/xamppfiles/htdocs/test-db-connection.php',
                'remote': f'{FTP_ROOT}/test-db-connection.php',
                'backup': False
            }
        ]
        
        # Process each file
        for file_info in files_to_upload:
            local_path = file_info['local']
            remote_path = file_info['remote']
            
            print(f"\nProcessing: {os.path.basename(remote_path)}")
            
            # Create backup if requested
            if file_info.get('backup', False):
                backup_file(ftp, remote_path)
            
            # Upload the file
            upload_file_ftp(local_path, remote_path, ftp)
        
        print("\n=== Upload Summary ===")
        print("1. .env file uploaded to server root")
        print("2. db_connections.php replaced with hardcoded version")
        print("3. test-db-connection.php uploaded for testing")
        print("\n=== Next Steps ===")
        print("1. Visit https://11klassniki.ru/test-db-connection.php to verify the connection")
        print("2. If successful, the database connection should be working")
        print("3. You can delete test-db-connection.php after testing")
        
        # Close FTP connection
        ftp.quit()
        print("\n✓ FTP connection closed")
        
    except Exception as e:
        print(f"\n✗ Error: {str(e)}")
        sys.exit(1)

if __name__ == "__main__":
    main()