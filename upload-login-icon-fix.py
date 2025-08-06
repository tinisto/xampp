#!/usr/bin/env python3

import ftplib
import os
import sys
from pathlib import Path

# FTP Configuration
FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
FTP_PATH = '/11klassnikiru/'

def upload_file(ftp, local_path, remote_path):
    """Upload a single file to FTP server"""
    try:
        with open(local_path, 'rb') as file:
            ftp.storbinary(f'STOR {remote_path}', file)
        print(f"✓ Uploaded: {local_path} -> {remote_path}")
        return True
    except Exception as e:
        print(f"✗ Failed to upload {local_path}: {e}")
        return False

def main():
    print("🔧 Uploading Login Icon Fix Files...")
    print("=" * 50)
    
    # Files to upload for login icon fix
    files_to_upload = [
        # Main login and registration files
        ('login-modern.php', 'login-modern.php'),
        ('registration-modern.php', 'registration-modern.php'),
        ('forgot-password.php', 'forgot-password.php'),
        ('pages/login/login-modern.php', 'pages/login/login-modern.php'),
        
        # Site icon component
        ('common-components/site-icon.php', 'common-components/site-icon.php'),
        ('common-components/favicon.php', 'common-components/favicon.php'),
        
        # Template engines with favicon updates
        ('common-components/template-engine-ultimate.php', 'common-components/template-engine-ultimate.php'),
        ('common-components/seo-head.php', 'common-components/seo-head.php'),
        
        # Form template with site icon
        ('includes/form-template-fixed.php', 'includes/form-template-fixed.php'),
    ]
    
    try:
        # Connect to FTP
        print(f"Connecting to {FTP_HOST}...")
        ftp = ftplib.FTP(FTP_HOST, FTP_USER, FTP_PASS)
        ftp.cwd(FTP_PATH)
        print("✓ Connected to FTP server")
        
        uploaded_count = 0
        total_files = len(files_to_upload)
        
        for local_file, remote_file in files_to_upload:
            local_path = local_file
            
            if os.path.exists(local_path):
                # Create directory if needed
                remote_dir = os.path.dirname(remote_file)
                if remote_dir:
                    try:
                        ftp.mkd(remote_dir)
                    except:
                        pass  # Directory might already exist
                
                if upload_file(ftp, local_path, remote_file):
                    uploaded_count += 1
            else:
                print(f"⚠ File not found: {local_path}")
        
        ftp.quit()
        
        print("=" * 50)
        print(f"✅ Upload complete: {uploaded_count}/{total_files} files uploaded")
        print("🔗 Test the login page: https://11klassniki.ru/login/")
        print("📝 Look for RED 'Зарегистрироваться' link to confirm changes")
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()