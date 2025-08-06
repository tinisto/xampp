#!/usr/bin/env python3

import ftplib
import os
import sys

# FTP Configuration
FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
FTP_PATH = '/11klassnikiru/'

def main():
    print("🔧 Write Page Header Fix")
    print("=" * 30)
    print("Removing duplicate 'Напишите нам' header")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST, FTP_USER, FTP_PASS)
        ftp.cwd(FTP_PATH)
        print("✓ Connected to FTP server")
        
        # Upload fixed file
        local_file = 'pages/write/write-simple.php'
        remote_file = 'pages/write/write-simple.php'
        
        with open(local_file, 'rb') as f:
            ftp.storbinary(f'STOR {remote_file}', f)
        
        print(f"✓ Uploaded: {local_file}")
        ftp.quit()
        
        print("=" * 30)
        print("✅ Fix complete!")
        print("🔗 Test: https://11klassniki.ru/write")
        print("📝 Should now show only one 'Напишите нам' title")
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()