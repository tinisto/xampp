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
    print("🔍 SPO/VPO Debug")
    print("=" * 20)
    
    try:
        # Connect to FTP
        ftp = ftplib.FTP(FTP_HOST, FTP_USER, FTP_PASS)
        ftp.cwd(FTP_PATH)
        print("✓ Connected to FTP server")
        
        # Upload files
        with open('pages/common/vpo-spo/single.php', 'rb') as f:
            ftp.storbinary('STOR pages/common/vpo-spo/single.php', f)
        print("✓ Uploaded: single.php")
        
        with open('pages/common/vpo-spo/single-debug.php', 'rb') as f:
            ftp.storbinary('STOR pages/common/vpo-spo/single-debug.php', f)
        print("✓ Uploaded: single-debug.php")
        
        ftp.quit()
        
        print("=" * 20)
        print("✅ Debug version active!")
        print("🔗 Test: https://11klassniki.ru/spo/belogorskiy-tehnologicheskiy-tehnikum-pischevoy-promyishlennosti")
        
    except Exception as e:
        print(f"❌ Upload failed: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()