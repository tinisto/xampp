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
    print("🔍 School Page Test")
    print("=" * 20)
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST, FTP_USER, FTP_PASS)
        ftp.cwd(FTP_PATH)
        print("✓ Connected to FTP server")
        
        # Upload test version
        with open('pages/school/school-single-test.php', 'rb') as f:
            ftp.storbinary('STOR pages/school/school-single.php', f)
        print("✓ Uploaded test version")
        
        ftp.quit()
        
        print("=" * 20)
        print("✅ Test version active!")
        print("🔗 Test: https://11klassniki.ru/school/ilezskaya-ileza")
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()