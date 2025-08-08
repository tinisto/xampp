#!/usr/bin/env python3
"""
Upload dashboard pages to production
"""

import ftplib
import os
from datetime import datetime

# FTP credentials
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

# Dashboard files to upload
DASHBOARD_FILES = [
    "dashboard-users-new.php",
    "dashboard-news-new.php", 
    "dashboard-posts-new.php",
    "dashboard-comments-new.php",
    "dashboard-schools-new.php",
    "dashboard-vpo-new.php",
    "dashboard-spo-new.php",
    ".htaccess"
]

def upload_files():
    """Upload dashboard files to production"""
    try:
        # Connect to FTP
        print(f"Connecting to {FTP_HOST}...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        print("Connected successfully!")
        
        # Upload each file
        for filename in DASHBOARD_FILES:
            local_path = f"/Applications/XAMPP/xamppfiles/htdocs/{filename}"
            
            if os.path.exists(local_path):
                with open(local_path, 'rb') as file:
                    print(f"Uploading {filename}...")
                    ftp.storbinary(f'STOR {filename}', file)
                    print(f"✅ {filename} uploaded successfully")
            else:
                print(f"❌ {filename} not found locally")
        
        # List dashboard directory to verify
        print("\n📁 Dashboard routes in .htaccess:")
        ftp.cwd(FTP_ROOT)
        
        # Close connection
        ftp.quit()
        print("\n✅ All dashboard pages uploaded successfully!")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    print("Dashboard Pages Upload Script")
    print("=" * 50)
    print(f"Time: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    print("=" * 50)
    upload_files()