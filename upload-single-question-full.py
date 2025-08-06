#!/usr/bin/env python3
"""
Upload Single Question Full Test Mode
====================================
Upload the new single-question full test interface
"""

import ftplib
import os
import sys

# FTP Configuration
FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
FTP_PATH = '/11klassnikiru/'

def upload_file(ftp, local_path, remote_path):
    """Upload a single file via FTP"""
    try:
        # Create remote directory if needed
        remote_dir = os.path.dirname(remote_path)
        if remote_dir != FTP_PATH.rstrip('/'):
            try:
                ftp.cwd(remote_dir)
            except ftplib.error_perm:
                # Directory doesn't exist, create it
                dirs = remote_dir.replace(FTP_PATH, '').split('/')
                current_path = FTP_PATH.rstrip('/')
                for dir_name in dirs:
                    if dir_name:
                        current_path += '/' + dir_name
                        try:
                            ftp.mkd(current_path)
                            print(f"Created directory: {current_path}")
                        except ftplib.error_perm:
                            pass  # Directory might already exist
        
        # Upload file
        with open(local_path, 'rb') as file:
            ftp.storbinary(f'STOR {remote_path}', file)
        print(f"✓ Uploaded: {local_path} -> {remote_path}")
        return True
    except Exception as e:
        print(f"✗ Failed to upload {local_path}: {e}")
        return False

def main():
    print("🚀 Upload Single Question Full Test Mode")
    print("=" * 40)
    
    # Files to upload
    files_to_upload = [
        {
            'local': '/Applications/XAMPP/xamppfiles/htdocs/pages/tests/test-full-single.php',
            'remote': '/11klassnikiru/pages/tests/test-full-single.php'
        },
        {
            'local': '/Applications/XAMPP/xamppfiles/htdocs/.htaccess',
            'remote': '/11klassnikiru/.htaccess'
        }
    ]
    
    try:
        print(f"Connecting to {FTP_HOST}...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        
        success_count = 0
        total_files = len(files_to_upload)
        
        for file_info in files_to_upload:
            local_path = file_info['local']
            remote_path = file_info['remote']
            
            if os.path.exists(local_path):
                if upload_file(ftp, local_path, remote_path):
                    success_count += 1
            else:
                print(f"✗ Local file not found: {local_path}")
        
        ftp.quit()
        
        print("\n" + "=" * 40)
        print(f"Upload complete: {success_count}/{total_files} files uploaded")
        
        if success_count > 0:
            print(f"\n✓ Single question full test mode is now available at:")
            print(f"  https://11klassniki.ru/test-full/russian-test")
            print(f"\nFeatures:")
            print(f"  • Shows one question at a time")
            print(f"  • No instant feedback (full test mode)")
            print(f"  • Navigation between questions")
            print(f"  • Results shown at the end")
            
    except Exception as e:
        print(f"Connection error: {e}")

if __name__ == "__main__":
    main()