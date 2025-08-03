#!/usr/bin/env python3
"""
Upload login form visibility fixes
"""

import ftplib
import os

def upload_file(ftp, local_path, remote_path):
    """Upload a single file to FTP server"""
    try:
        # Navigate to remote directory
        remote_dir = os.path.dirname(remote_path)
        ftp.cwd('/11klassnikiru')
        
        if remote_dir:
            dirs = remote_dir.split('/')
            for dir in dirs:
                if dir:
                    try:
                        ftp.cwd(dir)
                    except:
                        ftp.mkd(dir)
                        ftp.cwd(dir)
        
        # Upload file
        with open(local_path, 'rb') as file:
            filename = os.path.basename(remote_path)
            ftp.storbinary(f'STOR {filename}', file)
            print(f"✅ Uploaded: {local_path}")
            return True
            
    except Exception as e:
        print(f"❌ Failed to upload {local_path}: {str(e)}")
        return False

def main():
    print("🚀 Uploading login form fixes...")
    
    # FTP Configuration
    FTP_HOST = "ftp.ipage.com"
    FTP_USER = "franko"
    FTP_PASS = "JyvR!HK2E!N55Zt"
    
    # Files to upload
    files_to_upload = [
        # CSS fix for login forms
        ('css/login-fix.css', 'css/login-fix.css'),
        
        # Fixed form template with CSS variables
        ('includes/form-template-fixed.php', 'includes/form-template-fixed.php'),
        
        # Updated login.php
        ('pages/login/login.php', 'pages/login/login.php'),
        
        # Modern login page alternative
        ('pages/login/login-modern.php', 'pages/login/login-modern.php'),
    ]
    
    try:
        print(f"📡 Connecting to {FTP_HOST}...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd('/11klassnikiru')
        print("✅ Connected to FTP server")
        
        # Upload each file
        for local_path, remote_path in files_to_upload:
            if os.path.exists(local_path):
                upload_file(ftp, local_path, remote_path)
            else:
                print(f"⚠️  File not found locally: {local_path}")
        
        ftp.quit()
        print("\n✅ Upload complete!")
        print("\n📝 Fixes applied:")
        print("1. Login form now uses CSS variables")
        print("2. Form fields are visible in both light and dark modes")
        print("3. Proper contrast for placeholders and inputs")
        print("4. Theme-aware alerts and buttons")
        print("\n🔍 Test the login page:")
        print("Main: https://11klassniki.ru/login")
        print("Alternative: https://11klassniki.ru/pages/login/login-modern.php")
            
    except Exception as e:
        print(f"❌ FTP connection failed: {str(e)}")

if __name__ == "__main__":
    main()