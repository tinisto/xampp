#!/usr/bin/env python3
import ftplib
import os

# FTP credentials
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def upload_file(ftp, local_path, remote_path):
    """Upload a single file to FTP server"""
    try:
        with open(local_path, 'rb') as file:
            ftp.storbinary(f'STOR {remote_path}', file)
        print(f"✓ Uploaded: {remote_path}")
        return True
    except Exception as e:
        print(f"✗ Failed to upload {remote_path}: {str(e)}")
        return False

def main():
    """Upload dropdown fix files"""
    files_to_upload = [
        ("common-components/real_header.php", "common-components/real_header.php"),
        ("test-dropdown.php", "test-dropdown.php")
    ]
    
    try:
        # Connect to FTP
        print("Connecting to FTP server...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        print("✓ Connected to FTP server")
        
        # Change to website root
        ftp.cwd(FTP_ROOT)
        
        # Upload files
        success_count = 0
        for local_file, remote_file in files_to_upload:
            local_path = f"/Applications/XAMPP/xamppfiles/htdocs/{local_file}"
            if upload_file(ftp, local_path, remote_file):
                success_count += 1
        
        print(f"\n✓ Successfully uploaded {success_count}/{len(files_to_upload)} files")
        
        # Close connection
        ftp.quit()
        print("✓ FTP connection closed")
        print("\nTest the dropdown at: https://11klassniki.ru/test-dropdown.php")
        
    except Exception as e:
        print(f"\n✗ Error: {str(e)}")
        return 1
    
    return 0

if __name__ == "__main__":
    exit(main())