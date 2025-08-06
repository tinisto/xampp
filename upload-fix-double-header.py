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
    print("🔧 Fix Double Header Issue")
    print("=" * 30)
    print("Removing duplicate page header from write page")
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST, FTP_USER, FTP_PASS)
        ftp.cwd(FTP_PATH)
        print("✓ Connected to FTP server")
        
        # Upload fixed files
        files = [
            ('pages/write/write.php', 'pages/write/write.php'),
            ('pages/write/write-content.php', 'pages/write/write-content.php')
        ]
        
        for local_file, remote_file in files:
            with open(local_file, 'rb') as f:
                ftp.storbinary(f'STOR {remote_file}', f)
            print(f"✓ Uploaded: {local_file}")
        
        ftp.quit()
        
        print("=" * 30)
        print("✅ Double header fixed!")
        print("🔗 Test: https://11klassniki.ru/write")
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()