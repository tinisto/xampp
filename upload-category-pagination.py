#!/usr/bin/env python3
"""
Upload category page with pagination
"""

import ftplib
import os
from datetime import datetime

# FTP credentials
FTP_HOST = "ftp.ipage.com"
FTP_USER = "franko"
FTP_PASS = "JyvR!HK2E!N55Zt"
FTP_ROOT = "/11klassnikiru"

def upload_file():
    """Upload category-working.php to production"""
    try:
        # Connect to FTP
        print(f"Connecting to {FTP_HOST}...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.cwd(FTP_ROOT)
        print("Connected successfully!")
        
        # Upload file
        filename = "category-working.php"
        local_path = f"/Applications/XAMPP/xamppfiles/htdocs/{filename}"
        
        if os.path.exists(local_path):
            with open(local_path, 'rb') as file:
                print(f"Uploading {filename}...")
                ftp.storbinary(f'STOR {filename}', file)
                print(f"✅ {filename} uploaded successfully")
                
                # Get file size on server to confirm
                size = ftp.size(filename)
                print(f"File size on server: {size} bytes")
        else:
            print(f"❌ {filename} not found locally")
        
        # Close connection
        ftp.quit()
        print("\n✅ Category page with pagination uploaded successfully!")
        print("\nTest the pagination at:")
        print("https://11klassniki.ru/category/11-klassniki")
        print("https://11klassniki.ru/category/11-klassniki?page=2")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")

if __name__ == "__main__":
    print("Category Page Pagination Upload")
    print("=" * 50)
    print(f"Time: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    print("=" * 50)
    upload_file()