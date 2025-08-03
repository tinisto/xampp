#!/usr/bin/env python3
"""
Upload remaining missing component files
"""

import ftplib
import os

def upload_file(ftp, local_path, remote_path):
    """Upload a single file to FTP server"""
    try:
        # Navigate to remote directory
        remote_dir = os.path.dirname(remote_path)
        if remote_dir:
            # Go back to root directory first
            ftp.cwd('/11klassnikiru')
            # Navigate to target directory
            ftp.cwd(remote_dir)
        
        # Upload file
        with open(local_path, 'rb') as file:
            filename = os.path.basename(remote_path)
            ftp.storbinary(f'STOR {filename}', file)
            print(f"✅ Uploaded: {local_path} -> {remote_path}")
            return True
            
    except Exception as e:
        print(f"❌ Failed to upload {local_path}: {str(e)}")
        return False

def main():
    """Upload missing component files"""
    print("🚀 Uploading missing component files...")
    
    # FTP Configuration
    FTP_HOST = "ftp.ipage.com"
    FTP_USER = "franko"
    FTP_PASS = "JyvR!HK2E!N55Zt"
    
    # Missing component files
    files_to_upload = [
        ('common-components/typography.php', 'common-components/typography.php'),
        ('common-components/image-lazy-load.php', 'common-components/image-lazy-load.php'),
        ('common-components/footer-unified.php', 'common-components/footer-unified.php'),
        ('includes/functions/pagination.php', 'includes/functions/pagination.php'),
    ]
    
    try:
        print(f"📡 Connecting to {FTP_HOST}...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd('/11klassnikiru')
        print("✅ Connected to FTP server")
        
        success_count = 0
        failed_count = 0
        
        for local_path, remote_path in files_to_upload:
            if os.path.exists(local_path):
                if upload_file(ftp, local_path, remote_path):
                    success_count += 1
                else:
                    failed_count += 1
            else:
                print(f"⚠️  File not found locally: {local_path}")
                failed_count += 1
        
        ftp.quit()
        
        print(f"\n📊 Upload Summary:")
        print(f"✅ Successfully uploaded: {success_count} files")
        print(f"❌ Failed uploads: {failed_count} files")
            
    except Exception as e:
        print(f"❌ FTP connection failed: {str(e)}")

if __name__ == "__main__":
    main()