#!/usr/bin/env python3
import ftplib
import os
import sys
from pathlib import Path

# FTP credentials
FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
REMOTE_DIR = '/11klassnikiru'

# Files to deploy
FILES_TO_DEPLOY = {
    # Main fixes
    'forgot-password-process.php': 'forgot-password-process.php',
    'forgot-password-standalone.php': 'forgot-password-standalone.php',
    'reset-password.php': 'reset-password.php',
    
    # Password reset confirmation pages
    'pages/account/reset-password/reset-password-confirm.php': 'pages/account/reset-password/reset-password-confirm.php',
    'pages/account/reset-password/reset-password-confirm-process.php': 'pages/account/reset-password/reset-password-confirm-process.php',
    'reset-password-confirm-standalone.php': 'reset-password-confirm-standalone.php',
    
    # Components
    'includes/components/site-logo.php': 'includes/components/site-logo.php',
    
    # Email templates
    'includes/email-templates/password-reset.php': 'includes/email-templates/password-reset.php',
    'includes/email-templates/password-changed.php': 'includes/email-templates/password-changed.php',
    'includes/email-templates/base-template.php': 'includes/email-templates/base-template.php',
    'includes/email-templates/admin-notification.php': 'includes/email-templates/admin-notification.php',
    'includes/email-templates/user-notification.php': 'includes/email-templates/user-notification.php',
    'includes/email-templates/email-service.php': 'includes/email-templates/email-service.php',
    
    # Test file
    'test-simple-email.php': 'test-simple-email.php',
}

def create_remote_directory(ftp, path):
    """Create directory structure on remote server"""
    dirs = path.split('/')
    for i in range(1, len(dirs)):  # Skip filename
        dir_path = '/'.join(dirs[:i])
        if dir_path:
            try:
                ftp.mkd(dir_path)
                print(f"📁 Created directory: {dir_path}")
            except ftplib.error_perm:
                pass  # Directory might already exist

def upload_file(ftp, local_file, remote_file):
    """Upload a single file to FTP server"""
    try:
        # Create remote directory if needed
        create_remote_directory(ftp, remote_file)
        
        # Upload the file
        with open(local_file, 'rb') as f:
            ftp.storbinary(f'STOR {remote_file}', f)
        print(f"✅ Uploaded: {remote_file}")
        return True
    except Exception as e:
        print(f"❌ Failed to upload {remote_file}: {str(e)}")
        return False

def main():
    print("🚀 Deploying Email System Fixes and Security Updates")
    print("===================================================")
    
    try:
        # Connect to FTP server
        print(f"\n📡 Connecting to {FTP_HOST}...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        print("✅ Connected successfully!")
        
        # Change to remote directory
        ftp.cwd(REMOTE_DIR)
        print(f"📂 Changed to directory: {REMOTE_DIR}")
        
        # Upload files
        success_count = 0
        total_files = len(FILES_TO_DEPLOY)
        
        print(f"\n📤 Uploading {total_files} files...")
        for local_file, remote_file in FILES_TO_DEPLOY.items():
            if os.path.exists(local_file):
                if upload_file(ftp, local_file, remote_file):
                    success_count += 1
            else:
                print(f"⚠️  Local file not found: {local_file}")
        
        # Close FTP connection
        ftp.quit()
        
        # Summary
        print(f"\n📊 Deployment Summary:")
        print(f"   - Total files: {total_files}")
        print(f"   - Successfully uploaded: {success_count}")
        print(f"   - Failed: {total_files - success_count}")
        
        if success_count == total_files:
            print("\n✅ All files deployed successfully!")
            print("\n🔒 Updates deployed:")
            print("   - Fixed password reset email sending")
            print("   - Removed token display from UI (security fix)")
            print("   - Proper email templates")
            print("   - Test email script available")
        else:
            print("\n⚠️  Some files failed to upload. Please check the errors above.")
        
        print("\n🌐 Test the email system:")
        print("   1. Test email sending: https://11klassniki.ru/test-simple-email.php?secret=test123&email=11klassniki.ru@gmail.com")
        print("   2. Password reset: https://11klassniki.ru/forgot-password")
        print("\n⚠️  Important: Remove test-simple-email.php after testing!")
        
    except Exception as e:
        print(f"\n❌ Error: {str(e)}")
        sys.exit(1)

if __name__ == "__main__":
    main()