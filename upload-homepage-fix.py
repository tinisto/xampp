#!/usr/bin/env python3

import ftplib
import os
import sys

# FTP settings
HOST = "ftp.ipage.com"
USER = "franko"
PASS = "JyvR!HK2E!N55Zt"
PATH = "/11klassnikiru/"

# Local base directory
LOCAL_BASE = "/Applications/XAMPP/xamppfiles/htdocs"

def upload_file(ftp, local_path, remote_path):
    """Upload a single file to FTP server"""
    try:
        ftp.cwd(PATH)
        with open(local_path, 'rb') as f:
            ftp.storbinary(f'STOR {remote_path}', f)
        print(f"✓ Uploaded: {remote_path}")
        return True
    except Exception as e:
        print(f"✗ Failed to upload {remote_path}: {str(e)}")
        return False

def main():
    print("FIXING HOMEPAGE GREEN HEADER")
    print("=" * 40)
    print("Uploading the CORRECT homepage file with green header")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(HOST)
        ftp.login(USER, PASS)
        ftp.cwd(PATH)
        print("✓ Connected to FTP")
        
        # Upload the correct homepage content file
        local_file = os.path.join(LOCAL_BASE, "index_content_posts_with_news_style.php")
        if upload_file(ftp, local_file, "index_content_posts_with_news_style.php"):
            print("✓ Homepage fixed!")
        else:
            print("✗ Failed to fix homepage")
            return 1
        
        ftp.quit()
        
        print("\n" + "=" * 40)
        print("NOW TEST: https://11klassniki.ru/")
        print("You should see the GREEN header with search!")
        print("=" * 40)
        
    except Exception as e:
        print(f"✗ FTP Error: {str(e)}")
        return 1
    
    return 0

if __name__ == "__main__":
    sys.exit(main())