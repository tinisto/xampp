#!/usr/bin/env python3

import ftplib
import os

# Connection details
HOST = "185.46.8.204"
USER = "u2666700"
PASS = "19Dima08Dima08"
REMOTE_PATH = "/domains/11klassniki.ru/public_html/"

def connect_and_upload():
    try:
        print("🔌 Attempting FTP connection...")
        
        # Create FTP connection
        ftp = ftplib.FTP()
        ftp.set_debuglevel(1)  # Show debug info
        
        print(f"Connecting to {HOST}...")
        ftp.connect(HOST, 21, timeout=60)
        
        print("Logging in...")
        ftp.login(USER, PASS)
        
        print("Getting current directory...")
        current_dir = ftp.pwd()
        print(f"Current directory: {current_dir}")
        
        # Try to change to target directory
        try:
            ftp.cwd(REMOTE_PATH)
            print(f"Changed to: {REMOTE_PATH}")
        except:
            print(f"Could not change to {REMOTE_PATH}, staying in current directory")
        
        # List current directory contents
        print("Directory contents:")
        files = ftp.nlst()
        for f in files[:10]:  # Show first 10 files
            print(f"  - {f}")
        
        print("✅ Connection successful!")
        
        # Try to upload one test file
        test_content = "<?php\n// Test file uploaded successfully\necho 'FTP upload working!';\n?>"
        
        print("📤 Uploading test file...")
        ftp.storbinary('STOR test-upload.php', open('/dev/stdin', 'rb'))
        
        ftp.quit()
        return True
        
    except Exception as e:
        print(f"❌ Connection failed: {e}")
        return False

if __name__ == "__main__":
    connect_and_upload()