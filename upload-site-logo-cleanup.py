#!/usr/bin/env python3

import ftplib
import os
import sys

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

def delete_file(ftp, remote_path):
    """Delete a file from FTP server"""
    try:
        ftp.delete(remote_path)
        print(f"✓ Deleted: {remote_path}")
        return True
    except Exception as e:
        print(f"⚠ Could not delete {remote_path}: {e}")
        return False

def main():
    print("🧹 Site Logo Cleanup - Remove Old Components")
    print("=" * 50)
    
    # Files to upload (updated with new site-icon)
    files_to_upload = [
        ('common-components/template-engine-ultimate.php', 'common-components/template-engine-ultimate.php'),
        ('pages/account/reset-password/reset-password-confirm-modern.php', 'pages/account/reset-password/reset-password-confirm-modern.php'),
        ('pages/account/reset-password/reset-password-confirm-content.php', 'pages/account/reset-password/reset-password-confirm-content.php'),
        ('pages/account/reset-password/reset-password-confirm-standalone.php', 'pages/account/reset-password/reset-password-confirm-standalone.php'),
    ]
    
    # Files to delete from server
    files_to_delete = [
        'includes/components/site-logo.php',
        'css/site-logo.css',
    ]
    
    try:
        # Connect to FTP
        print(f"Connecting to {FTP_HOST}...")
        ftp = ftplib.FTP(FTP_HOST, FTP_USER, FTP_PASS)
        ftp.cwd(FTP_PATH)
        print("✓ Connected to FTP server")
        
        # Upload updated files
        print("\n📤 Uploading updated files...")
        uploaded_count = 0
        for local_file, remote_file in files_to_upload:
            if os.path.exists(local_file):
                # Create directory if needed
                remote_dir = os.path.dirname(remote_file)
                if remote_dir:
                    try:
                        ftp.mkd(remote_dir)
                    except:
                        pass  # Directory might already exist
                
                if upload_file(ftp, local_file, remote_file):
                    uploaded_count += 1
            else:
                print(f"⚠ File not found: {local_file}")
        
        # Delete old files
        print(f"\n🗑️ Removing old site-logo files...")
        deleted_count = 0
        for remote_file in files_to_delete:
            if delete_file(ftp, remote_file):
                deleted_count += 1
        
        ftp.quit()
        
        print("=" * 50)
        print(f"✅ Cleanup complete:")
        print(f"   📤 {uploaded_count}/{len(files_to_upload)} files updated")
        print(f"   🗑️ {deleted_count}/{len(files_to_delete)} old files removed")
        print(f"\n🎉 All authentication pages now use consistent site-icon component!")
        print(f"   • Green '11-классники' logo everywhere")
        print(f"   • No more old circle SVG logos")
        print(f"   • Unified favicon across all pages")
        
    except Exception as e:
        print(f"❌ Cleanup failed: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()