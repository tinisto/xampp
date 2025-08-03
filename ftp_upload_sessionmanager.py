#!/usr/bin/env python3
"""
Upload missing SessionManager.php file to server
"""

import ftplib
import os

def upload_sessionmanager():
    """Upload SessionManager.php to fix the missing file error"""
    
    # FTP Configuration
    FTP_HOST = "ftp.ipage.com"
    FTP_USER = "franko"
    FTP_PASS = "JyvR!HK2E!N55Zt"
    
    local_file = 'includes/SessionManager.php'
    remote_path = 'includes/SessionManager.php'
    
    if not os.path.exists(local_file):
        print(f"❌ Local file not found: {local_file}")
        return False
    
    try:
        print("🚀 Uploading SessionManager.php...")
        print(f"📡 Connecting to {FTP_HOST}...")
        
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd('/11klassnikiru')
        
        print("✅ Connected to FTP server")
        
        # Navigate to includes directory
        ftp.cwd('includes')
        
        # Upload the file
        with open(local_file, 'rb') as file:
            ftp.storbinary('STOR SessionManager.php', file)
            print(f"✅ Uploaded: {local_file} -> {remote_path}")
        
        ftp.quit()
        print("\n🎉 SessionManager.php deployed successfully!")
        return True
        
    except Exception as e:
        print(f"❌ Upload failed: {str(e)}")
        return False

if __name__ == "__main__":
    upload_sessionmanager()