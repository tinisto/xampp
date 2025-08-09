#!/usr/bin/env python3
"""
Upload fixed test file to production server
"""

import ftplib
import os

# FTP Configuration
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def upload_file(ftp, local_path, remote_path):
    """Upload a single file"""
    try:
        # Upload file
        with open(local_path, 'rb') as file:
            ftp.storbinary(f'STOR {remote_path}', file)
        print(f"✅ Uploaded: {local_path} -> {remote_path}")
        return True
    except Exception as e:
        print(f"❌ Failed to upload {local_path}: {str(e)}")
        return False

def main():
    print("🚀 Uploading fixed test file...")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        print("✅ Connected to FTP server")
        
        # Change to website root
        ftp.cwd(FTP_ROOT)
        
        # First delete the old broken file
        try:
            ftp.delete("/test-comment-system.php")
            print("✅ Deleted old test file")
        except:
            print("ℹ️  Old test file not found or already deleted")
        
        # Upload the fixed version
        if upload_file(ftp, "test-comment-system-fixed.php", "/test-comment-system.php"):
            print("\n✅ Test file fixed and uploaded successfully!")
            print("\n📋 You can now test the comment system at:")
            print("https://11klassniki.ru/test-comment-system.php")
        else:
            print("\n❌ Failed to upload test file")
        
        # Close connection
        ftp.quit()
        
    except Exception as e:
        print(f"❌ FTP Error: {str(e)}")
        return 1
    
    return 0

if __name__ == "__main__":
    exit(main())