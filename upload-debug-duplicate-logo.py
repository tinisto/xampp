#!/usr/bin/env python3
import ftplib
import os
from pathlib import Path

# FTP connection details
FTP_HOST = "92.53.96.236"
FTP_USER = "u309384953"
FTP_PASS = "7$ZF3K=Nmdw;"
FTP_ROOT = "/domains/11klassniki.ru/public_html"

def upload_file(ftp, local_path, remote_path):
    """Upload a single file to FTP server"""
    try:
        # Ensure remote directory exists
        remote_dir = os.path.dirname(remote_path)
        if remote_dir and remote_dir != '/':
            try:
                ftp.mkd(remote_dir)
            except:
                pass  # Directory might already exist
        
        # Upload file
        with open(local_path, 'rb') as file:
            ftp.storbinary(f'STOR {remote_path}', file)
        print(f"✓ Uploaded: {remote_path}")
        return True
    except Exception as e:
        print(f"✗ Failed to upload {remote_path}: {str(e)}")
        return False

def main():
    """Upload debug page"""
    try:
        # Connect to FTP
        print("Connecting to FTP server...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        print("✓ Connected to FTP server")
        
        # Change to website root
        ftp.cwd(FTP_ROOT)
        
        # Upload debug page
        local_file = "/Applications/XAMPP/xamppfiles/htdocs/debug-duplicate-logo.php"
        remote_file = "debug-duplicate-logo.php"
        
        if upload_file(ftp, local_file, remote_file):
            print("\n✓ Debug page uploaded successfully!")
            print(f"Visit: https://11klassniki.ru/{remote_file}")
        
        # Close connection
        ftp.quit()
        print("\n✓ FTP connection closed")
        
    except Exception as e:
        print(f"\n✗ Error: {str(e)}")
        return 1
    
    return 0

if __name__ == "__main__":
    exit(main())